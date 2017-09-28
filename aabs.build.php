<?php

aabs_sync("LineageOS");
aabs_sync("NexusOS");
aabs_sync("ResurrectionRemix");
aabs_sync("AOKP");

build_rom("LineageOS", "lineage");
build_rom("NexusOS", "lineage");
build_rom("ResurrectionRemix", "lineage");
build_rom("AOKP", "aokp");

function build_rom($rom, $device_prefix) {
	// check if ROM is disabled
	if (AABS_ROMS != "*" && strpos(AABS_ROMS . " ", "{$rom} ") === false) {
		return;
	}

	aabs_build($rom, $device_prefix, array(
		// G92[0/5]F/I
		'zerofltexx' => array(
			'clean'   => array( "lineage_zerofltexx-ota-*.zip", "lineage-*-zerofltexx.zip" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "lineage_zerofltexx-ota-*.zip",
			'targets' => "bacon",
		),
		'zeroltexx' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),

		// G92[0/5]P (and maybe more...)
		'zerofltespr' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),
		'zeroltespr' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),

		// G92[0/5]T/W8
		'zerofltecan' => array(
			'clean'   => array( "lineage_zerofltecan-ota-*.zip", "lineage-*-zerofltecan.zip" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "lineage_zerofltecan-ota-*.zip",
			'targets' => "bacon",
		),
		'zeroltecan' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "boot.img",
			'targets' => "bootimage",
		),

		// G92[0/5]S/K/L
		'zeroflteskt' => array(
			'clean'   => array( "lineage_zeroflteskt-ota-*.zip", "lineage-*-zeroflteskt.zip" ),
			'clobber' => AABS_SOURCE_CLOBBER,
			'match'   => "lineage_zeroflteskt-ota-*.zip",
			'targets' => "bacon",
		),
		'zerolteskt' => array(
			'clean'   => array( "boot.img" ),
			'clobber' => AABS_SOURCE_CLOBBER,
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