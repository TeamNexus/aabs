<?php

function upload_to_sftp($data) {
	$host = $data['remote']['host'];
	$port = $data['remote']['port'];
	$user = $data['remote']['user'];
	$pass = $data['remote']['pass'];

	$output = $data['output'];
	$md5sum = $data['md5sum'];
	$uploaddir = $data['upload']['dir'];
	$uploadfile = $data['upload']['file'];
	
	echo "Connecting to " . $host . ":" . $port . "...\n";
	if (!$ssh_conn = ssh2_connect($host, $port))
		die("aabs_upload: failed to establish connection to " . $host . ":" . $port);

	echo "Authenicating...\n";
	if (!ssh2_auth_password($ssh_conn, $user, $pass))
		die("aabs_upload: failed to login to " . $host . ":" . $port . " (Using password: " . ($pass == "" ? "no" : "yes") . ")");

	echo "Creating SFTP-session...\n";
	if (!$sftp_conn = ssh2_sftp($connection))
		die("aabs_upload: failed to create a SFTP-session");

	echo "Creating upload-directory...\n";
	if (!ssh2_exec($ssh_conn, "mkdir -p \"{$uploaddir}\""))
		die("aabs_upload: failed to create upload-directory");

	echo "Uploading build...\n";
	upload($sftp_conn, $output, $uploaddir, "{$uploadfile}");
	
	echo "Make build visible...\n";
	if (!ssh2_exec($ssh_conn, "mv \"$uploaddir/.$uploadfile\" \"$uploaddir/$uploadfile\""))
		die("aabs_upload: failed to rename uploaded build-file");

	echo "Uploading md5sum...\n";
	upload($sftp_conn, $md5sum, $uploaddir, "{$uploadfile}.md5sum");
}

function upload($sftp_conn, $local_file, $upload_dir, $upload_file) {
	$remote_stream = @fopen("ssh2.sftp://$sftp_conn$upload_dir/.$upload_file", 'w');
	$local_stream  = @fopen($output, 'r');

	if (!flock($local_stream, LOCK_SH))
		die("aabs_upload: failed to acquire lock for local file");

	$total   = filesize($local_file);
	$current = 0;

	echo "\rUploading \"{$local_file}\": {$current} / {$total}...";

	while(!feof($local_stream)) {
		$buffer = fread($local_stream, 8192);
		fwrite($remote_stream, $buffer, strlen($buffer));

		$current += strlen($buffer);
		echo "\rUploading \"{$local_file}\": {$current} / {$total}...";
	}
	fflush($remote_stream);

	fclose($remote_stream);
	fclose($local_stream);

	echo "\rUploading \"{$local_file}\": {$current} / {$total}\n";
}
