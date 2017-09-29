<?php

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
