<?php

function __mkdir($name) {
	if(!is_dir($name))
		__exec("mkdir -p {$name}");
}

function __exec__no_assert($cmdline, $censoring = array( )) {
	$output   = array( );
	$rc       = 0;
	$dcmdline = $cmdline;
		
	if (strpos($cmdline, "\n") !== false) {
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
	system("{$cmdline}", $rc);
	
	return $rc;
}

function __exec($cmdline, $censoring = array( )) {
	$rc = __exec__no_assert($cmdline, $censoring);
	if ($rc != 0) {
		die("Previous command failed with {$rc}\n");
	}
}

function __exec__allow_single_error($cmdline, $code, $censoring = array( )) {
	$rc = __exec__no_assert($cmdline, $censoring);
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
