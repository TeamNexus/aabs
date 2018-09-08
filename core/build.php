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

function aabs_build($rom, $lunch_rom, $lunch_flavor, $targets_combinations) {
	// check if build is disabled
	if (AABS_SKIP_BUILD) {
		return;
	}

	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS . ",", "{$rom},") === false) {
		return;
	}

	// check if ROM is supported and existing
	if (!validate_rom($rom)) {
		return;
	}

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
	$command .= 'if [ -x $RR_BUILDTYPE ]; then' . "\n";
	$command .= '	export RR_BUILDTYPE=Unofficial' . "\n";
	$command .= 'fi' . "\n";
	$command .= "\n";
	$command .= 'if [ -x $WITH_ROOT_METHOD ]; then' . "\n";
	$command .= '   export WITH_ROOT_METHOD="magisk"' . "\n";
	$command .= 'fi' . "\n";
	$command .= "\n";
	$command .= 'source build/envsetup.sh' . "\n";
	$command .= "\n";

	foreach($targets_combinations as $device => $cmd) {
		// check if device is disabled
		if (AABS_DEVICES != "*" && strpos(AABS_DEVICES . ",", "{$device},") === false) {
			continue;
		}

		$clean   = isset($cmd['clean']) ? $cmd['clean'] : array( );
		$clobber = isset($cmd['clobber']) ? $cmd['clobber'] : false;
		$jobs	= isset($cmd['jobs']) ? $cmd['jobs'] : AABS_BUILD_JOBS;
		$match   = isset($cmd['match']) ? $cmd['match'] : "";
		$targets = isset($cmd['targets']) ? $cmd['targets'] : "bacon";

		if (AABS_IS_DRY_RUN) {
			echo "building '$targets' for '$device' (clean: " . ($clean ? "true" : "false") . ", clobber: " . ($clobber ? "true" : "false") . ", jobs: $jobs)\n";
			continue;
		}

		$envvars = explode(",", AABS_ENV_VARIABLES);
		foreach ($envvars as $envvar) {
			if (empty($envvar)) {
				continue;
			}

			$envdata = explode('=', $envvar, 2);

			if (count($envdata) == 0) {
				die("invalid environment variable: \"{$envvar}\"");
			} elseif (count($envdata) == 1) {
				$envname = $envdata[0];
				$envval  = "true";
			} else {
				$envname = $envdata[0];
				$envval  = $envdata[1];
			}

			$command .= "export {$envname}={$envval}\n" . $__assert;
			$command .= "\n";
		}
		$command .= "\n";

		foreach ($clean as $clean_file) {
			$clean_path = "out/target/product/{$device}/" . $clean_file;

			$command .= 'rm -rfv ' . $clean_path . "\n" . $__assert;
			$command .= 'rm -rfv ' . $clean_path . '*' . "\n" . $__assert;
			$command .= "\n";
		}

		$command .= 'lunch ' . $lunch_rom . '_' . $device . '-' . $lunch_flavor . "\n" . $__assert;

		if ($clobber) {
			$command .= 'make clobber -j' . $jobs . "\n" . $__assert;
		} else {
			$command .= 'make installclean -j' . $jobs . "\n" . $__assert;
		}

		// build.prop
		$sysprops_target = get_output_directory($rom, $device, AABS_SOURCE_BASEDIR . "/{$rom}") . '/system/build.prop';

		// build-targets
		$command .= 'make ' . $sysprops_target  . ' ' . $targets . ' -j' . $jobs . "\n" . $__assert;

		$command .= "\n";
	}

	if (!AABS_IS_DRY_RUN)
		xexec($command);
}