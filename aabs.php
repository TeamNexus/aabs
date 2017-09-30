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

define('AABS_BASEDIR', dirname($argv[0]));
define('AABS_START_TIME', time());

// include global constants
include AABS_BASEDIR . "/core/const.php";

// include utilities
include AABS_BASEDIR . "/core/utils.php";

// include remote-plugins
include AABS_BASEDIR . "/remote/ftp.php";
include AABS_BASEDIR . "/remote/local.php";
include AABS_BASEDIR . "/remote/mega.php";
include AABS_BASEDIR . "/remote/sftp.php";

// include core-functions
include AABS_BASEDIR . "/core/sync.php";
include AABS_BASEDIR . "/core/build.php";
include AABS_BASEDIR . "/core/patch.php";
include AABS_BASEDIR . "/core/upload.php";

// parse arguments, load configurations and start building
include AABS_BASEDIR . "/core/core.php";
