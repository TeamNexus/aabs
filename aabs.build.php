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
	));
	aabs_patch($rom, array(
		'silence' => true,
		'log_indention' => ($rom == "NexusOS" ? "    " : "")
	), "zerofltexx", "{$lunch_rom}_zerofltexx-ota-*.zip", array(
		'zerofltexx' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zeroflte',
				'zerofltedv',
				'zerofltektt',
				'zerofltelgt',
				'zeroflteskt',
			)
		),
		'zeroltexx' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zerolte',
				'zeroltedv',
				'zeroltektt',
				'zeroltelgt',
				'zerolteskt',
			)
		),
		'zerofltecan' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zerofltebmc',
				'zerofltetmo',
				'zeroflteue',
				'zerofltemtr',
			)
		),
		'zeroltecan' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zeroltebmc',
				'zeroltetmo',
			)
		),
	));

	/*
	 * G92[0/5]P (and maybe more...)
	 */
	aabs_build($rom, $lunch_rom, 'userdebug', array(
		'zerofltespr' => array(
			'clean'   => array( "{$lunch_rom}_zerofltespr-ota-*.zip" ),
			'clobber' => false,
			'match'   => "{$lunch_rom}_zerofltespr-ota-*.zip",
			'targets' => "otapackage",
		),
		'zeroltespr' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
	));
	aabs_patch($rom, array(
		'silence' => true,
		'log_indention' => ($rom == "NexusOS" ? "    " : "")
	), "zerofltespr", "{$lunch_rom}_zerofltespr-ota-*.zip", array(
		'zerofltespr' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zeroflteacg',
				'zerofltechn',
				'zerofltectc',
				'zeroflteusc',
				'zerofltezc',
				'zerofltezh',
				'zerofltezm',
				'zerofltezt',
			)
		),
		'zeroltespr' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zerolteacg',
				'zeroltechn',
				'zerolteusc',
				'zeroltezc',
				'zeroltezt',
			)
		),
	));

	aabs_upload_multi($rom, "zero", array( 'jobs' => 4 ), array(
		// G92[0/5]F/I/S/K/L
		'zerofltexx' => array(
			'match' => "{$lunch_rom}_zerofltexx-ota-*.zip",
			'type'  => BUILD_TYPE_BUILD,
			'var-overrides' => array(
				'device' => "zero-multitarget",
			),
		),

		// G92[0/5]P (and maybe more...)
		'zerofltespr' => array(
			'match' => "{$lunch_rom}_zerofltespr-ota-*.zip",
			'type'  => BUILD_TYPE_BUILD,
		),
	));
}
