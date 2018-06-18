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
		case BUILD_TYPE_PATCH:
			$type_name = "Patch";
			$type_fileext = "zip";
			break;
		case BUILD_TYPE_BOOT:
			$type_name = "Kernel";
			$type_fileext = "img";
			break;
		case BUILD_TYPE_RECOVERY:
			$type_name = "Recovery";
			$type_fileext = "img";
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
