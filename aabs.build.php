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

	aabs_build($rom, $lunch_rom, 'user', array(
		// G92[0/5]F/I
		'zerofltexx' => array(
			'clean'   => array( "lineage_zerofltexx-ota-*.zip", "lineage-*-zerofltexx.zip" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "lineage_zerofltexx-ota-*.zip",
			'targets' => "bacon",
		),
		'zeroltexx' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),

		// G92[0/5]P (and maybe more...)
		'zerofltespr' => array(
			'clean'   => array( "lineage_zerofltespr-ota-*.zip", "lineage-*-zerofltespr.zip" ),
			'clobber' => false,
			'match'   => "lineage_zerofltespr-ota-*.zip",
			'targets' => "bacon",
		),
		'zeroltespr' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),

		// G92[0/5]T/W8
		'zerofltecan' => array(
			'clean'   => array( "lineage_zerofltecan-ota-*.zip", "lineage-*-zerofltecan.zip" ),
			'clobber' => false,
			'match'   => "lineage_zerofltecan-ota-*.zip",
			'targets' => "bacon",
		),
		'zeroltecan' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),

		// G92[0/5]S/K/L
		'zeroflteskt' => array(
			'clean'   => array( "lineage_zeroflteskt-ota-*.zip", "lineage-*-zeroflteskt.zip" ),
			'clobber' => false,
			'match'   => "lineage_zeroflteskt-ota-*.zip",
			'targets' => "bacon",
		),
		'zerolteskt' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => false,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
	));

	// builds
	aabs_upload($rom, "zero", "zerofltexx", "lineage_zerofltexx-ota-*.zip", BUILD_TYPE_BUILD);
	aabs_upload($rom, "zero", "zeroflteskt", "lineage_zeroflteskt-ota-*.zip", BUILD_TYPE_BUILD);

	// kernels
	aabs_upload($rom, "zero", "zeroltexx", "boot.img", BUILD_TYPE_BOOT);
	aabs_upload($rom, "zero", "zerofltespr", "boot.img", BUILD_TYPE_BOOT);
	aabs_upload($rom, "zero", "zeroltespr", "boot.img", BUILD_TYPE_BOOT);
	aabs_upload($rom, "zero", "zerolteskt", "boot.img", BUILD_TYPE_BOOT);
}
