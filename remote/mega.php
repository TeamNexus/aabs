<?php

function upload_to_mega($data) {
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];
	$output = $data['output']['path'];
	$outputdir = $data['output']['dir'];
	$outputfile = $data['output']['file'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];

	// login
	$user = str_replace("\"", "\\\"", $user);
	$pass = str_replace("\"", "\\\"", $pass);

	$login = __exec_ret("mega-login \"{$user}\" \"{$pass}\"", array( $pass ), array( 202, 255 ));
	if ($login !== 0 && !== 202) {
		__exec("screen -d -S \"aabs-mega-cmd\" -m \"mega-cmd\"");
		sleep(5);
		__exec_ret("mega-login \"{$user}\" \"{$pass}\"", array( $pass ), array( 202 ));
	}

	// upload file
	__exec("mega-put {$output} -c {$uploaddir}/{$uploadfile}");
}
