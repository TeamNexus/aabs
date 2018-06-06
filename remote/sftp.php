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

use phpseclib\Net\SFTP;

function upload_to_sftp($data) {
	$host = $data['remote']['host'];
	$port = $data['remote']['port'];
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];

	$output = $data['output'];
	$props = dirname($data['output']) . '/system/build.prop';
	$hashes = $data['hashes'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];

	$out_total     = filesize($output);
	$speed_time    = round(microtime(true) * 1000);
	$speed_current = 0;

	$uploadprgcb = function ($current) use($output, $out_total, &$speed_time, &$speed_current) {
		$percentage       = round($current / $out_total, 4) * 100;
		$speed_time_curr  = round(microtime(true) * 1000);

		$speed_time_diff = ($speed_time_curr - $speed_time);
		if ($speed_time_diff >= 10) {
			$speed_current_diff = $current - $speed_current;
			$speed = ($speed_current_diff / ($speed_time_diff / 1000));

			$displ_speed   = round($speed     / 1024       , 3);
			$displ_current = round($current   / 1024 / 1024, 3);
			$displ_total   = round($out_total / 1024 / 1024, 3);

			printf("\rUploading \"%s\": %10.3f MB / %10.3f MB  @  %10.3f KB/s  (%6.2f%%)... ",
				$output, $displ_current, $displ_total, $displ_speed, $percentage);

			$speed_current = $current;
			$speed_time    = $speed_time_curr;
		}
	};

	echo "Connecting to " . $host . ":" . $port . "...\n";
	$sftp = new SFTP($host, $port);

	echo "Authenticating...\n";
	if (!$sftp->login($user, $pass)) {
		die("aabs_upload: failed to login to " . $host . ":" . $port . " (Using password: " . ($pass == "" ? "no" : "yes") . ")");
	}

	echo "Creating upload-directory...\n";
	if (!$sftp->mkdir($uploaddir, 0775, true)) {
		// if creation failed, check if it exists
		if (!$sftp->realpath($uploaddir)) {
			die("aabs_upload: failed to create to upload-directory");
		}
	}

	echo "Uploading build...\n";
	$speed_time = round(microtime(true) * 1000);
	if (!$sftp->put("${uploaddir}/.${uploadfile}", $output, SFTP::SOURCE_LOCAL_FILE, -1, -1, $uploadprgcb)) {
		die("aabs_upload: failed to upload build");
		
	}
	call_user_func($uploadprgcb, $out_total);
	echo "\n";

	echo "Make build visible...\n";
	if (!$sftp->rename("${uploaddir}/.${uploadfile}", "${uploaddir}/${uploadfile}")) {
		die("aabs_upload: failed to rename uploaded build");
	}

	echo "Uploading properties...\n";
	if (!$sftp->put("${uploaddir}/${uploadfile}.prop", $props, SFTP::SOURCE_LOCAL_FILE, -1, -1, null)) {
		die("aabs_upload: failed to upload properties");
	}

	echo "Uploading checksums...\n";
	foreach ($hashes as $hash => $hashfile) {
		echo "\t- {$hash}sum...\n";
		$sftp->put("${uploaddir}/{$uploadfile}.{$hash}sum", $hashfile, SFTP::SOURCE_LOCAL_FILE);
	}
}
