<?php

function upload_to_mega($data) {
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];
	$output = $data['output']['path'];
	$outputdir = $data['output']['dir'];
	$outputfile = $data['output']['file'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];
	
	// check for megacmd
	__exec("command -v mega-cmd >/dev/null 2>&1 || exit 1");
	
	// login
	$user = str_replace("\"", "\\\"", $user);
	$pass = str_replace("\"", "\\\"", $pass);
	__exec__allow_single_error("mega-login \"{$user}\" \"{$pass}\"", 202, array( $pass )); /* Allow "Already logged in."-error */
	
	// rename output file
	$new_output = $outputdir . '/' . $uploadfile;
	__exec("mv {$output} {$new_output}");
	
	// upload file
	__exec("mega-put {$new_output} -c {$uploaddir}");
	
	// rename it back
	__exec("mv {$new_output} {$output}");
}
