<?php

function upload_to_local($data) {
	$output = $data['output'];
	$md5sum = $data['md5sum'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];
	$uploadpath = "{$uploaddir}/{$uploadfile}";

	echo "Creating upload-directory...\n";
	__exec("mkdir -p \"{$uploaddir}\"");

	echo "Uploading build...\n";
	__exec("cp -p \"{$output}\" \"{$uploadpath}\"");

	echo "Uploading md5sum...\n";
	__exec("cp -p \"{$md5sum}\" \"{$uploadpath}.md5sum\"");
}
