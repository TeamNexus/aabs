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

aabs_sync("LineageOS");
aabs_sync("NexusOS");
aabs_sync("ResurrectionRemix");
aabs_sync("AOKP");

build_rom("LineageOS", "lineage");
build_rom("NexusOS", "lineage");
build_rom("ResurrectionRemix", "lineage");
build_rom("AOKP", "aokp");

function build_rom($rom, $lunch_rom) {
	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS . " ", "{$rom} ") === false) {
		return;
	}

	aabs_build($rom, $lunch_rom, 'userdebug', array(
		// G92[0/5]F/I
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
		// G92[0/5]FD
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

		// G92[0/5]P (and maybe more...)
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

		// G92[0/5]T/W8
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

		// G92[0/5]S/K/L
		'zeroflteskt' => array(
			'clean'   => array( "{$lunch_rom}_zeroflteskt-ota-*.zip" ),
			'clobber' => false,
			'match'   => "{$lunch_rom}_zeroflteskt-ota-*.zip",
			'targets' => "otapackage",
		),
		'zerolteskt' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
	));

	// builds
	aabs_upload($rom, "zero", "zerofltexx", "{$lunch_rom}_zerofltexx-ota-*.zip", BUILD_TYPE_BUILD);
	aabs_upload($rom, "zero", "zeroflteduo", "{$lunch_rom}_zeroflteduo-ota-*.zip", BUILD_TYPE_BUILD);
	aabs_upload($rom, "zero", "zerofltespr", "{$lunch_rom}_zerofltespr-ota-*.zip", BUILD_TYPE_BUILD);
	aabs_upload($rom, "zero", "zerofltecan", "{$lunch_rom}_zerofltecan-ota-*.zip", BUILD_TYPE_BUILD);
	aabs_upload($rom, "zero", "zeroflteskt", "{$lunch_rom}_zeroflteskt-ota-*.zip", BUILD_TYPE_BUILD);

	// kernels
	aabs_upload($rom, "zero", "zeroltexx", "boot.img", BUILD_TYPE_BOOT);
	aabs_upload($rom, "zero", "zerolteduo", "boot.img", BUILD_TYPE_BOOT);
	aabs_upload($rom, "zero", "zeroltespr", "boot.img", BUILD_TYPE_BOOT);
	aabs_upload($rom, "zero", "zeroltecan", "boot.img", BUILD_TYPE_BOOT);
	aabs_upload($rom, "zero", "zerolteskt", "boot.img", BUILD_TYPE_BOOT);
}
