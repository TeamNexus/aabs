<?php
/*
 * Copyright (C) 2017 Lukas Berger <mail@lukasberger.at>
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
	$updater_script = file_get_contents("{$extracted_dir}/META-INF/com/google/android/updater-script");

	// print debugging/support-infos
	$scripting_debug_output =
		'ui_print("This patched OTA-package supports:");' . "\n" .
		'ui_print(" ");' . "\n";
	foreach ($targets as $target_device => $target) {
		$scripting_debug_output .= 'ui_print("  - ' . $target_device . '");' . "\n";
		foreach ($target['aliases'] as $target_alias)
			$scripting_debug_output .= 'ui_print("  - ' . $target_alias . '");' . "\n";
	}
	$updater_script =
		'ui_print(" ");' . "\n" .
		'ui_print("Patched by AABS");' . "\n" .
		'ui_print("https://github.com/TeamNexus/aabs");' . "\n" .
		'ui_print(" ");' . "\n" .
		$scripting_debug_output .
		'ui_print(" ");' . "\n" .
		$updater_script;

	// extract kernel-flash command
	if (!preg_match('/package_extract_file\("boot\.img", "(.+)"\);/', $updater_script, $scripting_kernel_preg)) {
		die("Cannot continue to patch OTA-package: Failed to find command for kernel-flashing\n");
	}
	$scripting_kernel_command = $scripting_kernel_preg[0];
	$scripting_kernel_target = $scripting_kernel_preg[1];

	// compose kernel flash-commands
	$kernel_targets = 0;
	$target_kernel_flash_commands = "";
	foreach ($targets as $target_device => $target) {
		if (!in_array('BOOT', $target['types'])) {
			continue;
		}

		$target_ota_file_path = "patches/boot-{$target_device}.img";

		// compose scripting-command
		$target_kernel_flash_command = 'getprop("ro.product.device") == "' . $target_device . '" || ' . 
				'getprop("ro.build.product") == "' . $target_device . '"';

		foreach ($target['aliases'] as $target_alias)
			$target_kernel_flash_command .= ' || getprop("ro.product.device") == "' . $target_alias . '" || ' . 
				'getprop("ro.build.product") == "' . $target_alias . '"';

		$target_kernel_flash_command =
			"if ({$target_kernel_flash_command}) then\n" .
			($options['silence'] ? "" : "{$options['log_indention']}ui_print(\"     Selecting kernel for target: {$target_device}\");\n") .
			($options['silence'] ? "" : "{$options['log_indention']}ui_print(\" \");\n") .
			"    package_extract_file(\"boot.img\", \"{$target_ota_file_path}\");\n" .
			"endif;\n";
		;

		// copy images
		$target_output_path = get_output_directory($rom, $target_device, $source_dir) . "/boot.img";
		xexec("cp -vf \"{$target_output_path}\" {$extracted_dir}/{$target_ota_file_path}");

		$target_kernel_flash_commands .= "\n{$target_kernel_flash_command}";
		$kernel_targets++;
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

	// clean up
	xexec("rm -rfv {$extracted_dir}");
	xexec("mv -fv {$output_path} {$output_path}.old");
	xexec("mv -fv {$output_path}.aabs.zip {$output_path}");
}