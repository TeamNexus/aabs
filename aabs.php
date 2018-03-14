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

define('AABS_BASEDIR', realpath(dirname($argv[0])));
define('AABS_START_TIME', time());

// include parameters
set_include_path(AABS_BASEDIR . PATH_SEPARATOR . AABS_BASEDIR . '/lib/phpseclib/vendor/phpseclib/phpseclib/phpseclib/');

// include global constants
include_once AABS_BASEDIR . "/core/const.php";

// include utilities
include_once AABS_BASEDIR . "/core/utils.php";

// include 3rd-party libraries
require AABS_BASEDIR . "/lib/phpseclib/vendor/autoload.php";

// include remote-plugins
include_once AABS_BASEDIR . "/remote/ftp.php";
include_once AABS_BASEDIR . "/remote/local.php";
include_once AABS_BASEDIR . "/remote/mega.php";
include_once AABS_BASEDIR . "/remote/sftp.php";

// include core-functions
include_once AABS_BASEDIR . "/core/sync.php";
include_once AABS_BASEDIR . "/core/build.php";
include_once AABS_BASEDIR . "/core/patch.php";
include_once AABS_BASEDIR . "/core/upload.php";
include_once AABS_BASEDIR . "/core/upload-multi.php";

// include classes
include_once AABS_BASEDIR . "/core/class/UploadTask.class.php";

// parse arguments, load configurations and start building
include_once AABS_BASEDIR . "/core/core.php";
