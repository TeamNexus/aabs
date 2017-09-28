<?php

function aabs_build($rom, $device_prefix, $targets_combinations) {
    // check if build is disabled
    if (AABS_SKIP_BUILD) {
        return;
    }

    // check if ROM is disabled
    if (AABS_ROMS != "*" && strpos(AABS_ROMS . " ", "{$rom} ") === false) {
        return;
    }

    // check if device is disabled
    if (AABS_DEVICES != "*" && strpos(AABS_DEVICES . " ", "{$device} ") === false) {
        return;
    }

    // check if ROM is supported and existing
    if (!__validate_rom($rom)) {
        return;
    }

    $__assert  = "";
    $__assert .= 'ret=$?' . "\n";
    $__assert .= 'if [ ! $ret -eq 0 ]; then' . "\n";
    $__assert .= "\t" . 'exit $ret' . "\n";
    $__assert .= 'fi' . "\n";
    $__assert .= "\n";

    $command  = "";
    $command .= '#!/bin/bash' . "\n";
    $command .= "\n";
    $command .= 'cd "' . AABS_SOURCE_BASEDIR . "/{$rom}" . '"' . "\n" . $__assert;
    $command .= "\n";
    $command .= 'export RR_BUILDTYPE=Unofficial' . "\n";
    $command .= 'export WITH_ROOT_METHOD="magisk"' . "\n";
    $command .= "\n";
    $command .= 'source build/envsetup.sh' . "\n";
    $command .= "\n";

    foreach($targets_combinations as $device => $cmd) {
        // check if device is disabled
        if (AABS_DEVICES != "*" && strpos(AABS_DEVICES, "{$device} ") === false) {
            continue;
        }

        $clean   = isset($cmd['clean']) ? $cmd['clean'] : array( );
        $clobber = isset($cmd['clobber']) ? $cmd['clobber'] : false;
        $jobs    = isset($cmd['jobs']) ? $cmd['jobs'] : AABS_BUILD_JOBS;
        $match   = isset($cmd['match']) ? $cmd['match'] : "";
        $targets = isset($cmd['targets']) ? $cmd['targets'] : "bacon";

        foreach ($clean as $clean_file) {
            $clean_path = "out/target/product/{$device}/" . $clean_file;

            $command .= "\n";
            $command .= 'rm -fv ' . $clean_path . "\n" . $__assert;
            $command .= 'rm -fv ' . $clean_path . '*' . "\n" . $__assert;
            $command .= "\n";
        }

        $command .= 'lunch ' . $device_prefix . '_' . $device . '-userdebug' . "\n" . $__assert;

        if ($clobber) {
            $command .= 'make clobber -j' . $jobs . "\n" . $__assert;
        }

        // build.prop
        $command .= 'make ' . AABS_SOURCE_BASEDIR . "/{$rom}" . '/out/target/product/' . $device . '/system/build.prop -j' . $jobs . "\n" . $__assert;

        // build-targets
        $command .= 'make ' . $targets . ' -j' . $jobs . "\n" . $__assert;

        $command .= "\n";
    }

    __exec($command);
}