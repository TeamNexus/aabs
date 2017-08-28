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

		$build_prop = file_get_contents("{$output_dir}/system/build.prop");
		
		$upload_dir = do_path_variables($rom, $device, $short_device, AABS_UPLOAD_DIR, $build_prop);
		$upload_file = do_path_variables($rom, $device, $short_device, AABS_UPLOAD_FILE, $build_prop);

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
