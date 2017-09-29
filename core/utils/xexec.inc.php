<?php

function xexec($cmdline, $censoring = array( )) {
	$rc = xexec_internal($cmdline, $censoring);
	if ($rc != 0) {
		die("Previous command failed with {$rc}\n");
	}
}
