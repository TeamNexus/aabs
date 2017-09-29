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

function upload_to_local($data) {
	$output = $data['output'];
	$hashes = $data['hashes'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];
	$uploadpath = "{$uploaddir}/{$uploadfile}";

	echo "Creating upload-directory...\n";
	xexec("mkdir -p \"{$uploaddir}\"");

	echo "Uploading build...\n";
	xexec("pv \"{$output}\" > \"{$uploaddir}/.{$uploadfile}\"");

	echo "Make build visible...\n";
	xexec("mv \"{$uploaddir}/.{$uploadfile}\" \"{$uploadpath}\"");

	foreach ($hashes as $hash => $file) {
		echo "Uploading {$hash}sum...\n";
		xexec("pv \"{$file}\" > \"{$uploadpath}.{$hash}sum\"");
	}
}
