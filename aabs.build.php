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

    aabs_build($rom, $device_prefix, "zerofltexx", array(
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
    ));

    // build
    aabs_upload($rom, "zero", "zerofltexx", "lineage_zerofltexx-ota-*.zip", BUILD_TYPE_BUILD);

    // kernels
    aabs_upload($rom, "zero", "zeroltexx", "boot.img", BUILD_TYPE_BOOT);
    aabs_upload($rom, "zero", "zerofltespr", "boot.img", BUILD_TYPE_BOOT);
    aabs_upload($rom, "zero", "zeroltespr", "boot.img", BUILD_TYPE_BOOT);
}
