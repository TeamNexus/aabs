<?php

function aabs_sync($rom) {
	// check if sync is disabled
	if (AABS_SKIP_SYNC) {
		return;
	}

	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS . " ", "{$rom} ") === false) {
		return;
	}

	// check if ROM is supported and existing
	if (!__validate_rom($rom)) {
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

	__exec($command);
}
