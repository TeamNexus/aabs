<?php
/*
 * Copyright (C) 2017-2018 Lukas Berger <mail@lukasberger.at>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!class_exists('UploadTask')) return;

function aabs_upload_multi($rom, $short_device, $options, $targets) {
	// check if uploading is disabled
	if (AABS_SKIP_UPLOAD) {
		return;
	}

	// check if ROM is supported and existing
	if (!validate_rom($rom)) {
		return;
	}

	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS . ",", "{$rom},") === false) {
		return;
	}

	// options
	$jobs = (isset($options['jobs']) ? (int)$options['jobs'] : 2);

	$pool = new Pool($jobs);
	foreach ($targets as $target_device => $target_data) {
		$pool->submit(new UploadTask($rom, $short_device, $target_device, $target_data['match'], $target_data['type']));
	}

	$pool->shutdown();
	echo "\nFinished!\n";
}
