<?php

    aabs_sync("ResurrectionRemix");
    aabs_sync("LineageOS");

    build_rom("ResurrectionRemix");
    build_rom("LineageOS");

    function build_rom($rom) {
        // check if ROM is disabled
        if (AABS_ROMS != "*" && strpos(AABS_ROMS, "{$rom} ") === false) {
            return;
        }

        // Samsung Galaxy S6 (zero, SM-G92xx)
        /* aabs_build($rom, "zerofltexx", array(
            'zerofltexx' => array(
                'clobber' => AABS_SOURCE_CLOBBER,
            ),
            'zeroltexx' => array(
                'targets' => "bootimage",
            ),
            'zerofltecan' => array(
                'targets' => "bootimage audio.primary.universal7420_32 audio.primary.universal7420",
            ),
            'zeroltecan' => array(
                'targets' => "bootimage audio.primary.universal7420_32 audio.primary.universal7420",
            )
        ));
        aabs_patch($rom, array( "zerofltexx", "zeroflte" ), array(
            'zeroltexx' => array(
                'alias' => array( 'zerolte' ),
                'files' => array(
                    "boot.img",
                ),
            ),
            'zerofltecan' => array(
                'alias' => array( 'zerofltetmo' ),
                'files' => array(
                    "boot.img",
                    "system/lib/hw/audio.primary.universal7420.so",
                    "system/lib64/hw/audio.primary.universal7420.so",
                    array( "device/samsung/zerofltecan/configs/audio/mixer_paths_0.xml", "system/etc/mixer_paths_0.xml" ),
                ),
            ),
            'zeroltecan' => array(
                'alias' => array( 'zeroltetmo' ),
                'files' => array(
                    "boot.img",
                    "system/lib/hw/audio.primary.universal7420.so",
                    "system/lib64/hw/audio.primary.universal7420.so",
                    array( "device/samsung/zeroltecan/configs/audio/mixer_paths_0.xml", "system/etc/mixer_paths_0.xml" ),
                ),
            ),
        )); */
        aabs_upload($rom, "zero", "zerofltexx");
    }
