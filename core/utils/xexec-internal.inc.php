<?php

function xexec_internal($cmdline, $censoring = array( )) {
	$output = array( );
	$rc = 0;
	$dcmdline = $cmdline;
	$tempfile = "";
	$is_external = (strpos($cmdline, "\n") !== false);

	if ($is_external) {
		$tempfile = tempnam(sys_get_temp_dir(), "aabs-exec-");
		file_put_contents($tempfile, $cmdline);
		chmod($tempfile, 0777);
		$dcmdline = $cmdline = "/bin/bash -c {$tempfile}";
	} else {
		foreach($censoring as $censor) {
			$dcmdline = str_replace($censor, "***", $dcmdline);
		}
	}

	echo "{$dcmdline}\n";
	passthru($cmdline);

	if ($is_external && $tempfile != "") {
		unlink($tempfile);
	}

	return $rc;
}
