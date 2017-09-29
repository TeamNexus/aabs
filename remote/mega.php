<?php

function upload_to_mega($data) {
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];

	$output = $data['output'];
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

	foreach ($hashes as $hash => $file) {
		echo "Uploading {$hash}sum...\n";
		xexec("mega-put \"{$file}\" -c \"{$uploaddir}/{$uploadfile}.{$hash}sum\"");
	}
}
