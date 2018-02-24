<?php
/*
 * Copyright (C) 2017 Lukas Berger <mail@lukasberger.at>
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
	));
	aabs_patch($rom, array(
		'silence' => false,
		'log_indention' => ($rom == "NexusOS" ? "    " : "")
	), "zerofltexx", "{$lunch_rom}_zerofltexx-ota-*.zip", array(
		'zerofltexx' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zeroflte',
				'zeroflteskt',
			)
		),
		'zeroltexx' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zerolte',
				'zerolteskt',
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
		'silence' => false,
		'log_indention' => ($rom == "NexusOS" ? "    " : "")
	), "zerofltespr", "{$lunch_rom}_zerofltespr-ota-*.zip", array(
		'zerofltespr' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zerofltechn',
				'zeroflteusc',
				'zerofltezt',
			)
		),
		'zeroltespr' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zeroltechn',
				'zerolteusc',
				'zeroltezt',
			)
		),
	));

	/*
	 * G92[0/5]T/W8
	 */
	aabs_build($rom, $lunch_rom, 'userdebug', array(
		'zerofltecan' => array(
			'clean'   => array( "{$lunch_rom}_zerofltecan-ota-*.zip" ),
			'clobber' => false,
			'match'   => "{$lunch_rom}_zerofltecan-ota-*.zip",
			'targets' => "otapackage",
		),
		'zeroltecan' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
	));
	aabs_patch($rom, array(
		'silence' => false,
		'log_indention' => ($rom == "NexusOS" ? "    " : "")
	), "zerofltecan", "{$lunch_rom}_zerofltecan-ota-*.zip", array(
		'zerofltecan' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zerofltetmo',
			)
		),
		'zeroltecan' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zeroltetmo',
			)
		),
	));

	/*
	 * G92[0/5]FD
	 */
	aabs_build($rom, $lunch_rom, 'userdebug', array(
		'zeroflteduo' => array(
			'clean'   => array( "{$lunch_rom}zeroflteduo-ota-*.zip" ),
			'clobber' => false,
			'match'   => "{$lunch_rom}zeroflteduo-ota-*.zip",
			'targets' => "otapackage",
		),
		'zerolteduo' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
	));
	aabs_patch($rom, array(
		'silence' => false,
		'log_indention' => ($rom == "NexusOS" ? "    " : "")
	), "zeroflteduo", "{$lunch_rom}_zeroflteduo-ota-*.zip", array(
		'zeroflteduo' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zerofltexx',
				'zeroflte',
				'zeroflteduo',
			)
		),
		'zerolteduo' => array(
			'types' => array( "BOOT" ),
			'aliases' => array(
				'zeroltexx',
				'zerolte',
				'zerolteduo',
			)
		),
	));

	aabs_upload_multi($rom, "zero", array( 'jobs' => 4 ), array(
		// G92[0/5]F/I/S/K/L
		'zerofltexx' => array(
			'match' => "{$lunch_rom}_zerofltexx-ota-*.zip",
			'type'  => BUILD_TYPE_BUILD,
		),

		// G92[0/5]P (and maybe more...)
		'zerofltespr' => array(
			'match' => "{$lunch_rom}_zerofltespr-ota-*.zip",
			'type'  => BUILD_TYPE_BUILD,
		),

		// G92[0/5]T/W8
		'zerofltecan' => array(
			'match' => "{$lunch_rom}_zerofltecan-ota-*.zip",
			'type'  => BUILD_TYPE_BUILD,
		),

		// G92[0/5]FD
		'zeroflteduo' => array(
			'match' => "{$lunch_rom}_zeroflteduo-ota-*.zip",
			'type'  => BUILD_TYPE_BUILD,
		),
	));
}
