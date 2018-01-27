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

function aabs_sync($rom) {
	// check if sync is disabled
	if (AABS_SKIP_SYNC) {
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
	$command .= 'repo sync -c -d -f --force-sync --no-clone-bundle --jobs=' . AABS_SYNC_JOBS . "\n" . $__assert;
	$command .= "\n";

	if (!AABS_IS_DRY_RUN) {
		xexec($command);
	} else {
		echo "syncing sources for '$rom'\n";
	}
}
