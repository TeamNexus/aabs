<?php

function upload_to_mega($data) {
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];
	$output = $data['output']['path'];
	$outputdir = $data['output']['dir'];
	$outputfile = $data['output']['file'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];

	// create temporary PID file
	/* $megapid = tempnam(sys_get_temp_dir(), "aabs-megacmd-pid-");
	file_put_contents($megapid, ""); */

	// check for megacmd
	__exec("command -v mega-cmd >/dev/null 2>&1 || exit 1");

	// start megacmd
	/* __exec__non_blocking(
		"#!/bin/bash" . "\n" .
		"mega-cmd &" . "\n" .
		"echo $! > \"{$megapid}\""
	); */

	// login
	$user = str_replace("\"", "\\\"", $user);
	$pass = str_replace("\"", "\\\"", $pass);
	__exec__allow_single_error("mega-login \"{$user}\" \"{$pass}\"", 202, array( $pass )); /* Allow "Already logged in."-error */

	// upload file
	__exec("mega-put {$output} -c {$uploaddir}/{$uploadfile}");

	// start megacmd
	/* __exec("kill " . file_get_contents($megapid)); */
}
