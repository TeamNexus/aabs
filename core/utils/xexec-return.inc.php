<?php

function xexec_return($cmdline, $censoring = array( ), $no_die_codes = array( )) {
	$rc = xexec_internal($cmdline, $censoring);

	if (in_array($rc, $no_die_codes)) {
		return $rc;
	}

	if ($rc != 0) {
		die("Previous command failed with {$rc}\n");
		return false;
	}

	return true;
}
