<?php
/*
 * Copyright (C) 2017 Lukas Berger <mail@lukasberger.at>
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

function upload_to_sftp($data) {
	$host = $data['remote']['host'];
	$port = $data['remote']['port'];
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];

	$output = $data['output'];
	$hashes = $data['hashes'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];

	echo "Connecting to " . $host . ":" . $port . "...\n";
	if (!$ssh_conn = ssh2_connect($host, $port))
		die("aabs_upload: failed to establish connection to " . $host . ":" . $port);

	echo "Authenicating...\n";
	if (!ssh2_auth_password($ssh_conn, $user, $pass))
		die("aabs_upload: failed to login to " . $host . ":" . $port . " (Using password: " . ($pass == "" ? "no" : "yes") . ")");

	echo "Creating SFTP-session...\n";
	if (!$sftp_conn = ssh2_sftp($ssh_conn))
		die("aabs_upload: failed to create a SFTP-session");

	echo "Creating upload-directory...\n";
	if (!ssh2_exec($ssh_conn, "mkdir -p \"{$uploaddir}\""))
		die("aabs_upload: failed to create upload-directory");

	echo "Uploading build...\n";
	upload($sftp_conn, $output, $uploaddir, "{$uploadfile}");

	echo "Make build visible...\n";
	if (!ssh2_exec($ssh_conn, "mv \"$uploaddir/.$uploadfile\" \"$uploaddir/$uploadfile\""))
		die("aabs_upload: failed to rename uploaded build-file");

	foreach ($hashes as $hash => $file) {
		echo "Uploading {$hash}sum...\n";
		upload($sftp_conn, $file, $uploaddir, "{$uploadfile}.{$hash}sum");
	}
}

function upload($sftp_conn, $local_file, $upload_dir, $upload_file) {
	$remote_stream = @fopen("ssh2.sftp://$sftp_conn$upload_dir/.$upload_file", 'w');
	$local_stream  = @fopen($local_file, 'r');

	if (!flock($local_stream, LOCK_SH))
		die("aabs_upload: failed to acquire lock for local file");

	$total   = filesize($local_file);
	$current = 0;

	printf("\rUploading \"%s\": %8.3f MB / %8.3f MB  @  %8.3d KB/s  (%5.2d%%)...",
		$local_file, 0, 0, 0, 0);

	$speed_time    = round(microtime(true) * 1000);
	$speed_current = 0;
	$speed         = 0;
	
	while(!feof($local_stream)) {
		$buffer = fread($local_stream, 8192);
		fwrite($remote_stream, $buffer, strlen($buffer));

		$current         += strlen($buffer);
		$percentage       = round($current / $total, 4) * 100;
		$speed_time_curr  = round(microtime(true) * 1000);

		$speed_time_diff = ($speed_time_curr - $speed_time);
		if ($speed_time_diff >= 1000) {
			$speed_current_diff = $current - $speed_current;
			$speed = ($speed_current_diff / ($speed_time_diff / 1000));

			$speed_current = $current;
			$speed_time    = $speed_time_curr;

			$displ_speed   = round($speed   / 1024       , 3);
			$displ_current = round($current / 1024 / 1024, 3);
			$displ_total   = round($total   / 1024 / 1024, 3);

			printf("\rUploading \"%s\": %8.3f MB / %8.3f MB  @  %8.3d KB/s  (%5.2f%%)...",
				$local_file, $displ_current, $displ_total, $displ_speed, $percentage);
		}
	}
	fflush($remote_stream);

	fclose($remote_stream);
	fclose($local_stream);

	echo "\rUploading \"{$local_file}\": {$current} / {$total}\n";
}
