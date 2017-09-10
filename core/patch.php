<?php

function aabs_patch($rom, $device, $targets = array( )) {
    // check if uploading is disabled
    if (AABS_SKIP_PATCH) {
        return;
    }

    // check if ROM is supported
    __validate_rom($rom);

    if (is_array($device)) {
        $device_aliases = $device;
        $device = $device[0];
    }

    // check if ROM is disabled
    if (AABS_ROMS != "*" && strpos(AABS_ROMS, "{$rom} ") === false) {
        return;
    }

    // check if device is disabled
    if (AABS_DEVICES != "*" && strpos(AABS_DEVICES, "{$device} ") === false) {
        return;
    }

    $source_dir  = AABS_SOURCE_BASEDIR . "/{$rom}";
    $output_dir  = "{$source_dir}/out/target/product/{$device}";
    $output_name = trim(shell_exec("/bin/bash -c \"basename $output_dir/" . __get_output_match($rom, $device) . "\""), "\n\t");
    $output_path = "{$output_dir}/{$output_name}";

    if (!is_file($output_path)) {
        die("Output not found: \"{$output_path}\"\n");
    }

    $tmp         = tempnam(sys_get_temp_dir(), 'aabs-patch-');

    if (is_file("$tmp"))
        unlink("$tmp");

    __mkdir("{$tmp}/");
    __mkdir("{$tmp}/patches/");

    __exec("unzip \"{$output_path}\" -d \"{$tmp}/\"");

    $script_targets = array( );
    foreach ($targets as $target_device => $target_options) {
        // check if device is disabled
        if (AABS_DEVICES != "*" && strpos(AABS_DEVICES, "{$device} ") === false) {
            continue;
        }

        $target_out_dir   = "{$source_dir}/out/target/product/{$target_device}";
        $target_patch_dir = "{$tmp}/patches/{$target_device}";

        if (!isset($script_targets[$target_device])) {
            $script_targets[$target_device] = array(
                'boot'   => false,
                'system' => false
            );
        }

        __mkdir("{$target_patch_dir}");

        foreach ($target_options['files'] as $target_file) {
            if (is_array($target_file)) {
                $target_file_src = "{$source_dir}/{$target_file[0]}";
                $target_file_dst = "{$target_patch_dir}/{$target_file[1]}";
            } else {
                $target_file_src = "{$target_out_dir}/{$target_file}";
                $target_file_dst = "{$target_patch_dir}/{$target_file}";
            }

            if ($target_file_dst == "{$target_patch_dir}/boot.img")
                $script_targets[$target_device]['boot'] = true;

            if (strpos($target_file_dst, "{$target_patch_dir}/system/") === 0)
                $script_targets[$target_device]['system'] = true;

            $target_file_dirname = dirname($target_file_dst);

            __mkdir("{$target_file_dirname}");
            __exec("cp -f {$target_file_src} {$target_file_dst}");
        }
    }

    $updater_script_path   = "{$tmp}/META-INF/com/google/android/updater-script";
    $updater_script        = file_get_contents($updater_script_path);

    $updater_script_asserts        = "";
    $updater_script_boot_asserts   = "";
    $updater_script_boot           = "";
    $updater_script_system_asserts = "";
    $updater_script_system         = "";

    $updater_device_assert = "getprop(\"ro.product.device\") == \"%%\" || getprop(\"ro.build.product\") == \"%%\" || ";

    if (preg_match("/package_extract_file\(\"boot.img\", \"([^\"]*)\"\);/", $updater_script, $boot_device_match) == 0) {
        die("Failed to get path of BOOT block-device\n");
    }

    foreach ($script_targets as $target => $flags) {
        if ($flags['boot']) {
            $target_device_asserts = str_replace("%%", $target, $updater_device_assert);
            foreach ($targets[$target]['alias'] as $alias) {
                $target_device_asserts .= str_replace("%%", $alias, $updater_device_assert);
            }

            $target_device_asserts        = substr($target_device_asserts, 0, strlen($target_device_asserts) - 4);
            $updater_script_boot_asserts .= $target_device_asserts . ' || ';

            $updater_script_boot .= "if {$target_device_asserts} then\n";
            $updater_script_boot .=     "ui_print(\"[AABS] Injecting kernel for {$target}\");\n";
            $updater_script_boot .=     "package_extract_file(\"patches/{$target}/boot.img\", \"{$boot_device_match[1]}\");\n";
            $updater_script_boot .= "endif;\n";
        }

        if ($flags['system']) {
            $target_device_asserts = str_replace("%%", $target, $updater_device_assert);
            foreach ($targets[$target]['alias'] as $alias) {
                $target_device_asserts .= str_replace("%%", $alias, $updater_device_assert);
            }

            $target_device_asserts          = substr($target_device_asserts, 0, strlen($target_device_asserts) - 4);
            $updater_script_system_asserts .= $target_device_asserts . ' || ';

            $updater_script_system .= "if {$target_device_asserts} then\n";
            $updater_script_system .=     "ui_print(\"[AABS] Injecting /system for {$target}\");\n";
            $updater_script_system .=     "package_extract_dir(\"patches/{$target}/system\", \"/system\");\n";
            $updater_script_system .= "endif;\n";
        }

        $updater_script_asserts .= $target_device_asserts . ' || ';
    }

    if ($updater_script_boot_asserts != "") {
        $boot_flash_command = substr($boot_device_match[0], 0, strlen($boot_device_match[0]) - 1);
        $updater_script_boot_asserts = substr($updater_script_boot_asserts, 0, strlen($updater_script_boot_asserts) - 4);

        $updater_script_boot .= "if !({$updater_script_boot_asserts}) then\n";
        $updater_script_boot .=     "ui_print(\"[AABS] Injecting default kernel\");\n";
        $updater_script_boot .=     "{$boot_flash_command}\n";
        $updater_script_boot .= "endif;\n";

        $updater_script = str_replace($boot_device_match[0], $updater_script_boot, $updater_script);
    }

    if ($updater_script_system_asserts != "") {
        $system_unmount_pos = strrpos($updater_script, "unmount(\"/system\");");
        $updater_script_system_asserts = substr($updater_script_system_asserts, 0, strlen($updater_script_system_asserts) - 4);

        $updater_script =
            substr($updater_script, 0, $system_unmount_pos) .
            $updater_script_system . "\n" .
            "if !({$updater_script_system_asserts}) then\n" .
                "ui_print(\"[AABS] No need to inject /system\");\n" .
            "endif;\n" .
            substr($updater_script, $system_unmount_pos, strlen($updater_script) - $system_unmount_pos);
    }

    foreach ($device_aliases as $device_alias) {
        $updater_script_asserts = str_replace("%%", $device_alias, $updater_device_assert) . $updater_script_asserts;
    }

    $updater_script_asserts = substr($updater_script_asserts, 0, strlen($updater_script_asserts) - 4);

    $updater_script_assert_line = strpos($updater_script, "\n");
    $updater_script = substr($updater_script, $updater_script_assert_line);

    $updater_script =
        "if !({$updater_script_asserts}) then\n" .
            "ui_print(\"\");\n" .
            "ui_print(\"\");\n" .
            "ui_print(\" *****************************************\");\n" .
            "ui_print(\" **                                     **\");\n" .
            "ui_print(\" **    SORRY, BUT THIS BUILD DOESN'T    **\");\n" .
            "ui_print(\" **         SUPPORT YOUR DEVICE         **\");\n" .
            "ui_print(\" **                                     **\");\n" .
            "ui_print(\" *****************************************\");\n" .
            "ui_print(\"\");\n" .
            "ui_print(\"\");\n" .
        "endif;\n" .
        "assert({$updater_script_asserts});\n" .
        "ui_print(\" \");\n" .
        "ui_print(\" \");\n" .
        "ui_print(\" Prepared and patched with    \");\n" .
        "ui_print(\" \");\n" .
        "ui_print(\"  _______ _______ ______   ______\");\n" .
        "ui_print(\" (_______|_______|____  \ / _____)\");\n" .
        "ui_print(\"  _______ _______ ____)  | (____\");\n" .
        "ui_print(\" |  ___  |  ___  |  __  ( \____ \\\\\");\n" .
        "ui_print(\" | |   | | |   | | |__)  )_____) )\");\n" .
        "ui_print(\" |_|   |_|_|   |_|______/(______/\");\n" .
        "ui_print(\" \");\n" .
        "ui_print(\" https://github.com/TeamNexus/aabs\");\n" .
        "ui_print(\" \");\n" .
        "ui_print(\" \");\n" .
        "ui_print(\" *********************************\");\n" .
        "ui_print(\" ROM-Name:      {$rom}\");\n" .
        "ui_print(\" Base-Device:   {$device}\");\n" .
        "ui_print(\" Timestamp:     " . date("Y-m-d H:i:s", AABS_START_TIME) . "\");\n" .
        "ui_print(\" *********************************\");\n" .
        "ui_print(\" \");\n" .
        $updater_script;

    file_put_contents($updater_script_path, $updater_script);

    __exec("mv {$output_path} {$output_path}.bak");
    __exec("cd {$tmp} && zip -r9 {$output_path} .");
    __exec("rm -rfv {$tmp}");
}