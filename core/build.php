<?php

function aabs_build($rom, $device_prefix, $main_device, $targets_combinations) {
	// check if build is disabled
	if (AABS_SKIP_BUILD) {
		return;
	}

	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS, "{$rom} ") === false) {
		return;
	}

	// check if device is disabled
	if (AABS_DEVICES != "*" && strpos(AABS_DEVICES, "{$device} ") === false) {
		return;
	}

	// check if ROM is supported
	__validate_rom($rom);

	$output_match_path = "out/target/product/{$main_device}/" . __get_output_match($rom, $main_device);

	$__assert  = "";
	$__assert .= 'ret=$?' . "\n";
	$__assert .= 'if [ ! $ret -eq 0 ]; then' . "\n";
	$__assert .= "\t" . 'exit $ret' . "\n";
	$__assert .= 'fi' . "\n";
	$__assert .= "\n";
		
	$command  = "";
	$command .= '#!/bin/bash' . "\n";
	$command .= "\n";
	$command .= 'cd "' . AABS_SOURCE_BASEDIR . "/{$rom}" . '"' . "\n" . $__assert;
	$command .= "\n";
	$command .= 'rm -fv ' . $output_match_path . "\n" . $__assert;
	$command .= 'rm -fv ' . $output_match_path . '.bak' . "\n" . $__assert;
	$command .= "\n";
	$command .= 'export RR_BUILDTYPE=Unofficial' . "\n";
	$command .= 'export WITH_ROOT_METHOD="magisk"' . "\n";
	$command .= "\n";
	$command .= 'source build/envsetup.sh' . "\n";
	$command .= "\n";
	$command .= 'rm -fv "' . $output_match_path . '"' . "\n" . $__assert;
	$command .= 'rm -fv "' . $output_match_path . '.bak"' . "\n" . $__assert;
	$command .= "\n";

	foreach($targets_combinations as $device => $cmd) {
		// check if device is disabled
		if (AABS_DEVICES != "*" && strpos(AABS_DEVICES, "{$device} ") === false) {
			continue;
		}

		$targets = isset($cmd['targets']) ? $cmd['targets'] : "bacon";
		$clobber = isset($cmd['clobber']) ? $cmd['clobber'] : false;
		$jobs = isset($cmd['jobs']) ? $cmd['jobs'] : AABS_BUILD_JOBS;

		$command .= 'lunch ' . $device_prefix . '_' . $device . '-userdebug' . "\n" . $__assert;
			
		if ($clobber) {
			$command .= 'make clobber -j' . $jobs . "\n" . $__assert;
		}
			
		$command .= 'make ' . $targets . ' -j' . $jobs . "\n" . $__assert;
		$command .= "\n";
	}
		
	__exec($command);
}