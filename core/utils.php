<?php

function __mkdir($name) {
	if(!is_dir($name))
		__exec("mkdir -p {$name}");
}

function __exec__internal($cmdline, $censoring = array( )) {
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
	passthru($cmdline, $rc);

	if ($is_external && $tempfile != "") {
		unlink($tempfile);
	}

	return $rc;
}

function __exec($cmdline, $censoring = array( )) {
	$rc = __exec__internal($cmdline, $censoring);
	if ($rc != 0) {
		die("Previous command failed with {$rc}\n");
	}
}

function __exec_ret($cmdline, $censoring = array( ), $no_die_codes = array( )) {
	$rc = __exec__internal($cmdline, $censoring);

	if (in_array($rc, $no_die_codes)) {
		return false;
	}

	if ($rc != 0) {
		die("Previous command failed with {$rc}\n");
		return false;
	}
	
	return true;
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

function do_path_variables($rom, $device, $short_device, $input, $build_prop) {
	$properties = array( );

	if (preg_match_all("/([a-zA-Z0-9\.\-\_]*)\=(.*)/", $build_prop, $prop_matches)) {
		$match_count = count($prop_matches[0]);
		for ($i = 0; $i < $match_count; $i++) {
			$key   = $prop_matches[1][$i];
			$value = $prop_matches[2][$i];
			
			$properties[$key] = $value;
		}
	}

	$input_len = strlen($input);

	// replace date and time
	for ($i = 0; $i < $input_len; $i++) {
		if ($input[$i] == '%' && $i + 1 < $input_len) {
			$input     = str_replace($input[$i] . $input[$i + 1], date($input[$i + 1], AABS_START_TIME), $input);
			$input_len = strlen($input);
		}
	}
	
	// replace build-properties
	foreach ($properties as $key => $value) {
		$input = str_replace("{PROP:{$key}}", $value, $input);
	}

	// replace static variables
	$input = str_replace("{ROM}", $rom, $input);
	$input = str_replace("{DEVICE}", $device, $input);
	$input = str_replace("{SHORT_DEVICE}", $short_device, $input);

	return $input;
}
