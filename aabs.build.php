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

// aabs_sync("NexusOS");
aabs_sync("LineageOS");
aabs_sync("ResurrectionRemix");
aabs_sync("AOKP");
aabs_sync("AICP");

build_rom("NexusOS", "lineage");
build_rom("LineageOS", "lineage");
build_rom("ResurrectionRemix", "rr");
build_rom("AOKP", "aokp");
build_rom("AICP", "aicp");

function build_rom($rom, $lunch_rom) {
	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS . ",", "{$rom},") === false) {
		return;
	}

	/*
	 * G92[0/5]F/I/S/K/L
	 */
	aabs_build($rom, $lunch_rom, 'userdebug', array(
		'zerofltexx' => array(
			'clean'   => array( "{$lunch_rom}_zerofltexx-ota-*.zip" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "{$lunch_rom}_zerofltexx-ota-*.zip",
			'targets' => "otapackage",
		),
		'zeroltexx' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
		'zerofltecan' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
		'zeroltecan' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
		'zerofltespr' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
		'zeroltespr' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
	));

	aabs_patch($rom, array(
		'silence' => false,
		'log_indention' => ($rom == "NexusOS" ? "     " : "")
	), "zerofltexx", "{$lunch_rom}_zerofltexx-ota-*.zip", array(
		'zerofltexx' => array(
			'types' => array( "BOOT" ),
			'models' => array(
				'G920F' => "zerofltexx",
				'G920I' => "zerofltedv",
				'G920S' => "zeroflteskt",
				'G920K' => "zerofltektt",
				'G920L' => "zerofltelgt",
			)
		),
		'zeroltexx' => array(
			'types' => array( "BOOT" ),
			'models' => array(
				'G925F' => "zeroltexx",
				'G925I' => "zeroltedv",
				'G925S' => "zerolteskt",
				'G925K' => "zeroltektt",
				'G925L' => "zeroltelgt",
			)
		),
		'zerofltecan' => array(
			'types' => array( "BOOT" ),
			'models' => array(
				'G920T1' => "zerofltemtr",
				'G920T' => "zerofltetmo",
				'G920W8' => "zerofltebmc",
			)
		),
		'zeroltecan' => array(
			'types' => array( "BOOT" ),
			'models' => array(
				'G925T1' => "zeroltemtr",
				'G925T' => "zeroltetmo",
				'G925W8' => "zeroltebmc",
			)
		),
		'zerofltespr' => array(
			'types' => array( "BOOT" ),
			'models' => array(
				'G9200'  => "zerofltezc",
				'G9208'  => "zerofltezm",
				'G9209'  => "zerofltectc",
				'G920P'  => "zerofltespr",
				'G920R4' => "zeroflteusc",
				'G920R7' => "zeroflteacg",
				'G920V'  => "zerofltevzw", // confirm this requires SPR
			)
		),
		'zeroltespr' => array(
			'types' => array( "BOOT" ),
			'models' => array(
				'G9250'  => "zeroltezc",
				'G9258'  => "zeroltezm",
				'G9259'  => "zeroltectc",
				'G925P'  => "zeroltespr",
				'G925R4' => "zerolteusc",
				'G925R7' => "zerolteacg",
				'G925V'  => "zeroltevzw", // confirm this requires SPR
			)
		),
	));

	aabs_upload_multi($rom, "zero", array( 'jobs' => 4 ), array(
		// G92[0/5]F/I/S/K/L/P
		'zerofltexx' => array(
			'match' => "{$lunch_rom}_zerofltexx-ota-*.zip",
			'type'  => BUILD_TYPE_BUILD,
			'var-overrides' => array(
				'device' => "zero-multitarget",
			),
		),
	));
}
