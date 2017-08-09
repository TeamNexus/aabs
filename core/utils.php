<?php

function __mkdir($name) {
	if(!is_dir($name))
		__exec("mkdir -p {$name}");
}

function __exec__internal($blocking, $cmdline, $censoring = array( )) {
	$output   = array( );
	$rc       = 0;
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

	if ($blocking) {
		passthru($cmdline, $rc);
	} else {
		$descriptorspec = array(
			0 => array( "pipe", "r" ),   // stdin is a pipe that the child will read from
			1 => array( "pipe", "w" ),   // stdout is a pipe that the child will write to
			2 => array( "pipe", "w" )    // stderr is a pipe that the child will write to
		);
		$process = proc_open("{$cmdline}", $descriptorspec, $pipes, realpath('./'), array());

		if (is_resource($process)) {
			// all pipes to non-blocking
			stream_set_blocking($pipes[0], 0);
			stream_set_blocking($pipes[1], 0);
			stream_set_blocking($pipes[2], 0);

			while ($line = fgets($pipes[1])) {
				print($line);
				flush();
			}
			$rc = proc_close($process);
		} else {
			$rc = 1;
		}
	}

	if ($is_external && $tempfile != "") {
		unlink($tempfile);
	}

	return $rc;
}

function __exec__internal__blocking($cmdline, $censoring = array( )) {
	return __exec__internal(true, $cmdline, $censoring);
}

function __exec__internal__non_blocking($cmdline, $censoring = array( )) {
	return __exec__internal(false, $cmdline, $censoring);
}

function __exec($cmdline, $censoring = array( )) {
	$rc = __exec__internal__blocking($cmdline, $censoring);
	if ($rc != 0) {
		die("Previous command failed with {$rc}\n");
	}
}

function __exec__non_blocking($cmdline, $censoring = array( )) {
	$rc = __exec__internal__non_blocking($cmdline, $censoring);
	if ($rc != 0) {
		die("Previous command failed with {$rc}\n");
	}
}

function __exec__allow_single_error($cmdline, $code, $censoring = array( )) {
	$rc = __exec__internal__blocking($cmdline, $censoring);
	if ($rc != 0 && $rc != $code) {
		die("Previous command failed with {$rc}\n");
	}
}

function __exec__allow_single_error__non_blocking($cmdline, $code, $censoring = array( )) {
	$rc = __exec__internal__non_blocking($cmdline, $censoring);
	if ($rc != 0 && $rc != $code) {
		die("Previous command failed with {$rc}\n");
	}
}
	
function __validate_rom($rom) {
	switch ($rom) {
		case "NexusOS":
		case "LineageOS":
		case "ResurrectionRemix":
		case "AOKP":
			return;
	}

	throw new Exception("Unsupported ROM: {$rom} (Supported: LineageOS, NexusOS, ResurrectionRemix, AOKP)");
}
	
function __get_output_match($rom, $device) {
	__validate_rom($rom);

	switch ($rom) {
		case "LineageOS":
		case "NexusOS":
		case "ResurrectionRemix":
			return "lineage_${device}-ota-*.zip";
		case "AOKP":
			return "aokp_${device}-ota-*.zip";
	}
}
