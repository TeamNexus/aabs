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
        if (AABS_ROMS != "*" && strpos(AABS_ROMS, "{$rom} ") === false) {
            return;
        }

		aabs_build($rom, $device_prefix, "zerofltexx", array(
            'zerofltexx' => array(
                'clobber' => AABS_SOURCE_CLOBBER,
            ),
            'zeroltexx' => array(
                'clobber' => AABS_SOURCE_CLOBBER,
                'targets' => "bootimage",
            ),
		);
        aabs_upload($rom, "zero", "zerofltexx", "lineage_${device}-ota-*.zip", BUILD_TYPE_BUILD);
        aabs_upload($rom, "zero", "zeroltexx", "boot.img", BUILD_TYPE_BOOT);
    }
