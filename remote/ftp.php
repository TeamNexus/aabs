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

function upload_to_ftp($data) {
	$host = $data['remote']['host'];
	$port = $data['remote']['port'];
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];

	$output = $data['output'];
	$props = dirname($data['output']) . '/system/build.prop';
	$hashes = $data['hashes'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];

	echo "Connecting to " . $host . ":" . $port . "...\n";
	if (!$ftp_conn = ftp_ssl_connect($host, $port))
		die("aabs_upload: failed to establish connection to " . $host . ":" . $port);

	echo "Authenicating...\n";
	if (!ftp_login($ftp_conn, $user, $pass))
		die("aabs_upload: failed to login to " . $host . ":" . $port . " (Using password: " . ($pass == "" ? "no" : "yes") . ")");

	echo "Switching to passive-mode...\n";
	if (!ftp_pasv($ftp_conn, true))
		die("aabs_upload: failed to switch to passive-mode");

	echo "Creating upload-directory...\n";
	if (!ftp_mksubdirs($ftp_conn, "/", $uploaddir))
		die("aabs_upload: failed to create upload-directory \"${uploaddir}\"");

	echo "Uploading build...\n";
	if (!ftp_put($ftp_conn, "${uploaddir}/.${uploadfile}", $output, FTP_BINARY))
		die("aabs_upload: failed to upload build to \"${uploaddir}/.${uploadfile}\"");

	echo "Make build visible...\n";
	if (!ftp_rename($ftp_conn, "$uploaddir/.$uploadfile", "$uploaddir/$uploadfile"))
		die("aabs_upload: failed to rename uploaded build-file");

	echo "Uploading properties...\n";
	if (!ftp_put($ftp_conn, "${uploaddir}/${uploadfile}.prop", $props, FTP_BINARY))
		die("aabs_upload: failed to upload properties to \"${uploaddir}/${uploadfile}.prop\"");

	foreach ($hashes as $hash => $file) {
		echo "Uploading {$hash}sum...\n";
		if (!ftp_put($ftp_conn, "${uploaddir}/${uploadfile}.{$hash}sum", $file, FTP_BINARY))
			die("aabs_upload: failed to upload {$hash}sum to \"${uploaddir}/${uploadfile}.{$hash}sum\"");
	}
}

// http://php.net/manual/en/function.ftp-mkdir.php#112399
function ftp_mksubdirs($ftpcon, $ftpbasedir, $ftpath) {
	@ftp_chdir($ftpcon, $ftpbasedir);
	$parts = explode('/', $ftpath);
	foreach($parts as $part) {
		if(!@ftp_chdir($ftpcon, $part)) {
			@ftp_mkdir($ftpcon, $part);
			@ftp_chdir($ftpcon, $part);
		}
	}
	return true;
}