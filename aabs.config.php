<?php

    /*
     * OVERRIDE: Enforce to skip the sync of the sources
     * @default false
     */
    /* if (!defined('AABS_SKIP_SYNC'))
        define('AABS_SKIP_SYNC', false); */

    /*
     * OVERRIDE: Enforce to skip the upload of the builds
     * @default false
     */
    /* if (!defined('AABS_SKIP_UPLOAD'))
        define('AABS_SKIP_UPLOAD', false); */

    /*
     * OVERRIDE: Enforced list of devices to build
     * @default "*"
     */
    /* if (!defined('AABS_DEVICES'))
        define('AABS_DEVICES', "*"); */

    /*
     * OVERRIDE: Enforced list of ROMs to build
     * @default "*"
     */
    /* if (!defined('AABS_ROMS'))
        define('AABS_ROMS', "*"); */

    /*
     * Count of concurrent jobs used to sync the sources
     * @default 4
     */
    if (!defined('AABS_SYNC_JOBS'))
        define('AABS_SYNC_JOBS', 4);

    /*
     * Directory which contains the repos of all ROMs
     * @default "/home"
     */
    if (!defined('AABS_SOURCE_BASEDIR'))
        define('AABS_SOURCE_BASEDIR', "/home");

    /*
     * Indicates if the build-output should be clobbered
     * @default true
     */
    if (!defined('AABS_SOURCE_CLOBBER'))
        define('AABS_SOURCE_CLOBBER', true);

    /*
     * Count of concurrent jobs used to build the ROM and the patches
     * @default 16
     */
    if (!defined('AABS_BUILD_JOBS'))
        define('AABS_BUILD_JOBS', 16);

    /*
     * Expression to match the build-output
     * @default "lineage_*.zip"
     */
    if (!defined('AABS_BUILD_OUTPUT_MATCH'))
        define('AABS_BUILD_OUTPUT_MATCH', "lineage_*.zip");

    /*
     * Indicates if uploads should be enabled
     * @default true
     */
    if (!defined('AABS_UPLOAD'))
        define('AABS_UPLOAD', true);

    /*
     * Hostname or IP of the upload-server
     * @default "server.exmaple.com"
     */
    if (!defined('AABS_UPLOAD_HOST'))
        define('AABS_UPLOAD_HOST', "server.exmaple.com");

    /*
     * Port of the upload-server
     * @default 22
     */
    if (!defined('AABS_UPLOAD_PORT'))
        define('AABS_UPLOAD_PORT', 22);

    /*
     * Username used to login to the upload-server
     * @default "root"
     */
    if (!defined('AABS_UPLOAD_USER'))
        define('AABS_UPLOAD_USER', "root");

    /*
     * Password used to login to the upload-server
     * @default "datpasswd"
     */
    if (!defined('AABS_UPLOAD_PASS'))
        define('AABS_UPLOAD_PASS', "datpasswd");

    /*
     * Directory to which the builds should be uploaded to
     * @default "/var/www/https/build/%Y-%m-%d_%H%i/{SHORT_DEVICE}"
     */
    if (!defined('AABS_UPLOAD_DIR'))
        define('AABS_UPLOAD_DIR', "/var/www/https/build/%Y-%m-%d_%H%i/{SHORT_DEVICE}");

    /*
     * New name of the uploaded build
     * @default "{ROM}-multitarget-%Y-%m-%d_%H%i.zip"
     */
    if (!defined('AABS_UPLOAD_FILE'))
        define('AABS_UPLOAD_FILE', "{ROM}-multitarget-%Y-%m-%d_%H%i.zip");
