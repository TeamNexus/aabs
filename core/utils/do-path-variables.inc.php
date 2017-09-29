<?php

function do_path_variables($rom, $device, $short_device, $type, $input, $build_prop) {
	// replace date and time
	$input_len = strlen($input);
	for ($i = 0; $i < $input_len; $i++) {
		if ($input[$i] == '%' && $i + 1 < $input_len) {
			$input	 = str_replace($input[$i] . $input[$i + 1], date($input[$i + 1], AABS_START_TIME), $input);
			$input_len = strlen($input);
		}
	}

	// replace build-properties
	$properties = parse_build_props($build_prop);
	foreach ($properties as $key => $value) {
		$input = str_replace("{PROP:{$key}}", $value, $input);
	}

	// generate dynamic variables
	$type_name = "";
	$type_fileext = "";
	switch ($type) {
		case BUILD_TYPE_BUILD:
			$type_fileext = "zip";
			break;
		case BUILD_TYPE_BOOT:
			$type_name = "Kernel";
			$type_fileext = "img";
			break;
		case BUILD_TYPE_PATCH:
			$type_name = "Patch";
			$type_fileext = "zip";
			break;
	}

	// replace static variables
	$input = str_replace("{ROM}", $rom, $input);
	$input = str_replace("{DEVICE}", $device, $input);
	$input = str_replace("{SHORT_DEVICE}", $short_device, $input);
	$input = str_replace("{TYPE_FILEEXT}", $type_fileext, $input);

	if ($type_name != "") {
		$input = str_replace("{-TYPE}", "-{$type_name}", $input);
		$input = str_replace("{-TYPE-}", "-{$type_name}-", $input);
		$input = str_replace("{TYPE-}", "{$type_name}-", $input);
	} else {
		$input = str_replace("{-TYPE}", "", $input);
		$input = str_replace("{-TYPE-}", "", $input);
		$input = str_replace("{TYPE-}", "", $input);
	}

	return $input;
}
