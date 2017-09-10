<?php

function upload_to_mega($data) {
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];

	$output = $data['output'];
	$md5sum = $data['md5sum'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];

	$user = str_replace("\"", "\\\"", $user);
	$pass = str_replace("\"", "\\\"", $pass);

	echo "Trying to login to mega.nz...\n";
	$login = __exec_ret("mega-login \"{$user}\" \"{$pass}\"", array( $pass ), array( 202, 255 ));
	if ($login !== 0 && $login !== 202) {
		__exec("screen -d -S \"aabs-mega-cmd\" -m \"mega-cmd\"");
		sleep(5);
		__exec_ret("mega-login \"{$user}\" \"{$pass}\"", array( $pass ), array( 202 ));
	}

	echo "Uploading build...\n";
	__exec("mega-put \"{$output}\" -c \"{$uploaddir}/{$uploadfile}\"");

	echo "Uploading md5sum...\n";
	__exec("mega-put \"{$md5sum}\" -c \"{$uploaddir}/{$uploadfile}.md5sum\"");
}
