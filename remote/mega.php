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

function upload_to_mega($data) {
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];

	$output = $data['output'];
	$props = dirname($data['output']) . '/system/build.prop';
	$hashes = $data['hashes'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];

	$user = str_replace("\"", "\\\"", $user);
	$pass = str_replace("\"", "\\\"", $pass);

	echo "Trying to login to mega.nz...\n";
	$login = xexec_return("mega-login \"{$user}\" \"{$pass}\"", array( $pass ), array( 202, 255 ));
	if ($login !== 0 && $login !== 202) {
		xexec("screen -d -S \"aabs-mega-cmd\" -m \"mega-cmd\"");
		sleep(5);
		xexec_return("mega-login \"{$user}\" \"{$pass}\"", array( $pass ), array( 202 ));
	}

	echo "Uploading build...\n";
	xexec("mega-put \"{$output}\" -c \"{$uploaddir}/{$uploadfile}\"");

	echo "Uploading properties...\n";
	xexec("mega-put \"{$props}\" -c \"{$uploaddir}/{$uploadfile}.prop\"");

	foreach ($hashes as $hash => $file) {
		echo "Uploading {$hash}sum...\n";
		xexec("mega-put \"{$file}\" -c \"{$uploaddir}/{$uploadfile}.{$hash}sum\"");
	}
}
