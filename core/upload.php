<?php

function aabs_upload($rom, $short_device, $device) {
		// check if uploading is disabled
		if (AABS_SKIP_UPLOAD) {
			return;
		}

		// check if ROM is supported
		__validate_rom($rom);

		// check if ROM is disabled
		if (AABS_ROMS != "*" && strpos(AABS_ROMS, "{$rom} ") === false) {
			return;
		}

		// check if device is disabled
		if (AABS_DEVICES != "*" && strpos(AABS_DEVICES, "{$device} ") === false) {
			return;
		}

		$source_dir	    = AABS_SOURCE_BASEDIR . "/{$rom}";
		$output_dir	    = "{$source_dir}/out/target/product/{$device}";
		$output_name    = trim(shell_exec("/bin/bash -c \"basename $output_dir/" . __get_output_match($rom, $device) . "\""), "\n\t");
		$output_path    = "{$output_dir}/{$output_name}";

		if (!is_file($output_path)) {
			die("Output not found: \"{$output_path}\"\n");
		}
		
		$upload_dir	 = AABS_UPLOAD_DIR;
		$upload_dir_len = strlen($upload_dir);
		for ($i = 0; $i < $upload_dir_len; $i++) {
			if ($upload_dir[$i] == '%' && $i + 1 < $upload_dir_len) {
				$upload_dir = str_replace($upload_dir[$i] . $upload_dir[$i + 1], date($upload_dir[$i + 1], AABS_START_TIME), $upload_dir);
				$upload_dir_len = strlen($upload_dir);
			}
		}
		$upload_dir = str_replace("{ROM}", $rom, $upload_dir);
		$upload_dir = str_replace("{DEVICE}", $device, $upload_dir);
		$upload_dir = str_replace("{SHORT_DEVICE}", $short_device, $upload_dir);

		$upload_file	 = AABS_UPLOAD_FILE;
		$upload_file_len = strlen($upload_file);
		for ($i = 0; $i < $upload_file_len; $i++) {
			if ($upload_file[$i] == '%' && $i + 1 < $upload_file_len) {
				$upload_file     = str_replace($upload_file[$i] . $upload_file[$i + 1], date($upload_file[$i + 1], AABS_START_TIME), $upload_file);
				$upload_file_len = strlen($upload_file);
			}
		}
		$upload_file = str_replace("{ROM}", $rom, $upload_file);
		$upload_file = str_replace("{DEVICE}", $device, $upload_file);
		$upload_file = str_replace("{SHORT_DEVICE}", $short_device, $upload_file);

		$fn = "";
		$params = array( );
		switch (AABS_UPLOAD_TYPE) {
			case "sftp":
				$fn = "upload_to_sftp";
				$params = array(
					'remote' => array(
						'host' => AABS_UPLOAD_HOST,
						'port' => AABS_UPLOAD_PORT,
						'user' => AABS_UPLOAD_USER,
						'pass' => AABS_UPLOAD_PASS,
					),
				);
				break;

			case "ftp": 
				$fn = "upload_to_ftp";
				$params = array(
					'remote' => array(
						'host' => AABS_UPLOAD_HOST,
						'port' => AABS_UPLOAD_PORT,
						'user' => AABS_UPLOAD_USER,
						'pass' => AABS_UPLOAD_PASS,
					),
				);
				break;

			case "mega":
				$fn = "upload_to_mega";
				$params = array(
					'remote' => array(
						'user' => AABS_UPLOAD_USER,
						'pass' => AABS_UPLOAD_PASS,
					),
				);
				break;
		}
		
		$params['output'] = array(
			'path'  => $output_path,
			'dir'  => $output_dir,
			'file' => $output_name,
		);
		$params['upload'] = array(
			'dir'  => $upload_dir,
			'file' => $upload_file,
		);
		
		$fn($params);

		echo "\nFinished!\n";
	}
