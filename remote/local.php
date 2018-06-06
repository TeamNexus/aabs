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

function upload_to_local($data) {
	$output = $data['output'];
	$props = dirname($data['output']) . '/system/build.prop';
	$hashes = $data['hashes'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];
	$uploadpath = "{$uploaddir}/{$uploadfile}";

	$cp_cmd_base = "pv -petI %{src} > %{dst}";
	if (!command_exists("pv")) {
		$cp_cmd_base = "rsync --progress --human-readable %{src} %{dst}";
		if (!command_exists("rsync")) {
			$cp_cmd_base = "cp -v %{src} %{dst}";
		}
	}

	echo "Creating upload-directory...\n";
	xexec("mkdir -p \"{$uploaddir}\"");

	echo "Uploading build...\n";
	$cp_cmd = __parse_cmd_base($cp_cmd_base, $output, "{$uploaddir}/.{$uploadfile}");
	xexec("{$cp_cmd}");

	echo "Make build visible...\n";
	xexec("mv \"{$uploaddir}/.{$uploadfile}\" \"{$uploadpath}\"");

	echo "Uploading properties...\n";
	$cp_cmd = __parse_cmd_base($cp_cmd_base, $props, "{$uploaddir}/{$uploadfile}.prop");
	xexec("{$cp_cmd}");

	foreach ($hashes as $hash => $file) {
		echo "Uploading {$hash}sum...\n";

		$cp_cmd = __parse_cmd_base($cp_cmd_base, $file, "{$uploadpath}.{$hash}sum");
		xexec("{$cp_cmd}");
	}
}

function __parse_cmd_base($cp_cmd_base, $src, $dst) {
	$cmd = str_replace("%{src}", $src, $cp_cmd_base);
	return str_replace("%{dst}", $dst, $cmd);
}