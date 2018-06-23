<?php
/*
 * Copyright (C) 2017-2018 Lukas Berger <mail@lukasberger.at>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

function aabs_patch($rom, $options, $device, $file_match, $targets) {
	// check if uploading is disabled
	if (AABS_SKIP_PATCH) {
		return;
	}

	// check if ROM is supported and existing
	if (!validate_rom($rom)) {
		return;
	}

	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS . ",", "{$rom},") === false) {
		return;
	}

	// check if device is disabled
	if (AABS_DEVICES != "*" && strpos(AABS_DEVICES . ",", "{$device},") === false) {
		return;
	}

	$source_dir  = AABS_SOURCE_BASEDIR . "/{$rom}";
	$output_dir  = get_output_directory($rom, $device, $source_dir);
	$output_name = trim(shell_exec("/bin/bash -c \"basename {$output_dir}/{$file_match}\""), "\n\t");
	$output_path = dirname("{$output_dir}/{$file_match}") . "/" . $output_name;

	if (AABS_IS_DRY_RUN)
		return; // unsupported for now

	// extract OTA-package
	$extracted_dir = tempnam(sys_get_temp_dir(), 'aabs-patch-');
	if (is_file($extracted_dir))
		unlink($extracted_dir);

	rmkdir("{$extracted_dir}/");
	rmkdir("{$extracted_dir}/patches/");
	xexec("unzip \"{$output_path}\" -d \"{$extracted_dir}/\"");

	// load updater-script
	$original_updater_script = file_get_contents("{$extracted_dir}/META-INF/com/google/android/updater-script");

	// print debugging/support-infos
	$target_device_list = "";
	foreach ($targets as $target_device => $target) {
		foreach ($target['models'] as $target_model => $target_name)
			$target_device_list .= "{$target_name},";
	}
	$target_device_list = substr($target_device_list, 0, strlen($target_device_list) - 1);

	$updater_script =
		'ui_print(" ");' . "\n" .
		'ui_print("Patched by AABS");' . "\n" .
		'ui_print("https://github.com/TeamNexus/aabs");' . "\n" .
		'ui_print(" ");' . "\n" .
		'ui_print("   Date:  ' . date('r') . '");' . "\n" .
		'ui_print("   ID:    ' . hash('md5', $device . $file_match . json_encode($options) . json_encode($targets)) . '");' . "\n" .
		'ui_print("   User:  ' . get_current_user() . '");' . "\n" .
		'ui_print("   Host:  ' . gethostname() . '");' . "\n" .
		'ui_print(" ");' . "\n" .
		'ui_print("This patched OTA-package supports:");' . "\n" .
		'ui_print(" ");' . "\n" .
		'ui_print("    ' . $target_device_list . '");' . "\n" .
		'ui_print(" ");' . "\n" .
		'assert(' .
			'(!less_than_int(getprop("ro.bootimage.build.date.utc"), ' . PATCH_MIN_RECOVERY_VERSION . ')) || ' .
			'abort("E3003: Current recovery is not supported by this build. Consider to update your recovery."););' . "\n" .
		$original_updater_script;

	// extract assert command
	if (!preg_match('/assert\(getprop\(".+this device is " \+ getprop\("ro\.product\.device"\) \+ "\."\);\);/s', $original_updater_script, $scripting_assert_preg)) {
		die("Cannot continue to patch OTA-package: Failed to find asserting command\n");
	}
	$scripting_assert_command = $scripting_assert_preg[0];

	// extract kernel-flash command
	if (!preg_match('/package_extract_file\("boot\.img", "(.+)"\);/', $original_updater_script, $scripting_kernel_preg)) {
		die("Cannot continue to patch OTA-package: Failed to find command for kernel-flashing\n");
	}
	$scripting_kernel_command = $scripting_kernel_preg[0];
	$scripting_kernel_target = $scripting_kernel_preg[1];

	// extract system-flash command
	if (!preg_match('/block_image_update\("(.+)", package_extract_file\("system\.transfer\.list"\), "system\.new\.dat\.br", "system\.patch\.dat"\) ' .
			'\|\|.+abort\("E1001: Failed to update system image\."\);/s',
			$original_updater_script, $scripting_system_preg)) {
		die("Cannot continue to patch OTA-package: Failed to find command for system-flashing\n");
	}
	$scripting_system_command = $scripting_system_preg[0];
	$scripting_system_target = $scripting_system_preg[1];

	// compose new assert command
	$target_assert_command = "";

	// compose kernel flash-commands
	$kernel_targets = 0;
	$target_kernel_flash_commands = "";
	foreach ($targets as $target_device => $target) {
		if (!in_array('BOOT', $target['types'])) {
			continue;
		}

		$target_ota_file_path = "patches/boot-{$target_device}.img";

		// compose scripting-command
		$target_kernel_flash_command = "";
		foreach ($target['models'] as $target_model => $target_name) {
			$target_kernel_flash_command .= 'starts_with("' . $target_model . '", getprop("ro.boot.bootloader")) || ';
		}
		$target_kernel_flash_command = substr($target_kernel_flash_command, 0, strlen($target_kernel_flash_command) - 4);

		if ($target_assert_command == "") {
			$target_assert_command = $target_kernel_flash_command;
		} else {
			$target_assert_command .= ' || ' . $target_kernel_flash_command;
		}

		$target_kernel_flash_command =
			"if ({$target_kernel_flash_command}) then\n" .
			($options['silence'] ? "" : "    ui_print(\"{$options['log_indention']}- Installing kernel for {$target_device}\");\n") .
			($options['silence'] ? "" : "    ui_print(\" \");\n") .
			"    package_extract_file(\"{$target_ota_file_path}\", \"{$scripting_kernel_target}\");\n" .
			"endif;\n";
		;

		// copy images
		$target_output_path = get_output_directory($rom, $target_device, $source_dir) . "/boot.img";
		xexec("cp -vf \"{$target_output_path}\" {$extracted_dir}/{$target_ota_file_path}");

		$target_kernel_flash_commands .= "\n{$target_kernel_flash_command}";
		$kernel_targets++;
	}

	/*
	 * Assert command post-processing
	 */
	$target_assert_command = 'assert(' . $target_assert_command . ' || ' .
		'abort("E3004: This package is for ' . $target_device_list . '; ' .
		'this device is " + getprop("ro.product.name") + "."););' . "\n";

	if (!$options['silence']) {
		foreach ($targets as $target_device => $target) {
			foreach ($target['models'] as $target_model => $target_name) {
				$target_assert_command .=
					'if (starts_with("' . $target_model . '", getprop("ro.boot.bootloader"))) then' . "\n";
				if ($target_name == $target_device) {
					$target_assert_command .= '    ui_print("' . $options['log_indention'] . '- Target: ' . $target_device . ' (' . $target_model . ')");' . "\n";
				} else {
					$target_assert_command .= '    ui_print("' . $options['log_indention'] . '- Target: ' . $target_device . ' (' . $target_model . '/' . $target_name . ')");' . "\n";
				}
				$target_assert_command .=
					'    ui_print(" ");' . "\n" .
					'endif;' . "\n";
			}
		}
		$target_assert_command .=
			'ui_print("' . $options['log_indention'] . '- Bootloader: " + getprop("ro.boot.bootloader"));' . "\n" .
			'ui_print(" ");' . "\n";
	}

	$updater_script = str_replace($scripting_assert_command, "\0/AABS_ASSERT_COMMAND_PLACEHOLDER\0/", $updater_script);
	$updater_script = str_replace("\0/AABS_ASSERT_COMMAND_PLACEHOLDER\0/", $target_assert_command, $updater_script);

	/*
	 * Kernel command post-processing
	 */
	if (!$options['silence']) {
		$target_kernel_flash_commands =
			'ui_print("' . $options['log_indention'] . '- Requesting kernel for " + getprop("ro.product.name"));' . "\n" .
			'ui_print(" ");' . "\n" .
			$target_kernel_flash_commands;
	}

	if ($kernel_targets != 0) {
		xexec("rm -vf \"{$extracted_dir}/boot.img\"");
		$updater_script = str_replace($scripting_kernel_command, "\0/AABS_KERNEL_COMMAND_PLACEHOLDER\0/", $updater_script);
		$updater_script = str_replace("\0/AABS_KERNEL_COMMAND_PLACEHOLDER\0/", $target_kernel_flash_commands, $updater_script);
	}

	// save updater-script
	file_put_contents("{$extracted_dir}/META-INF/com/google/android/updater-script", $updater_script);

	// repack OTA-package
	xexec("rm -fv {$output_path}.aabs.zip");
	xexec("cd {$extracted_dir}; zip -r5 {$output_path}.aabs.zip .");

	// sign OTA-package if requested
	if (AABS_SIGN) {
		$base_output_dir = get_base_output_directory($rom, $source_dir);

		xexec("mv -fv {$output_path}.aabs.zip {$output_path}.unsigned.aabs.zip");

		// sign OTA
		xexec("cd {$source_dir}/; java " .
			"-Xmx" . AABS_SIGN_MEMORY_LIMIT . " " .
			"-Djava.library.path={$base_output_dir}/host/linux-x86/lib64 " .
			"-jar {$base_output_dir}/host/linux-x86/framework/signapk.jar " .
			"-w " . AABS_SIGN_PUBKEY . " " . AABS_SIGN_PRIVKEY . " " .
			"{$output_path}.unsigned.aabs.zip " .
			"{$output_path}.aabs.zip");
	}

	// clean up
	xexec("rm -rfv {$extracted_dir}");
	xexec("mv -fv {$output_path} {$output_path}.old");
	xexec("mv -fv {$output_path}.aabs.zip {$output_path}");
}