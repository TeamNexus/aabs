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

/*
 * Default value for skipping syncing sources
 * @default false
 */
if (!defined('AABS_SKIP_SYNC'))
	define('AABS_SKIP_SYNC', false);

/*
 * Default value for skipping building
 * @default false
 */
if (!defined("AABS_SKIP_BUILD"))
	define("AABS_SKIP_BUILD", false);

/*
 * Default value for skipping build-patching
 * @default false
 */
if (!defined("AABS_SKIP_PATCH"))
	define("AABS_SKIP_PATCH", false);

/*
 * Default value for skipping uploading builds
 * @default false
 */
if (!defined('AABS_SKIP_UPLOAD'))
	define('AABS_SKIP_UPLOAD', false);

/*
 * Default list of devices to build
 * @default "*"
 */
if (!defined('AABS_DEVICES'))
	define('AABS_DEVICES', "*");

/*
 * Default list of ROMs to build
 * @default "*"
 */
if (!defined('AABS_ROMS'))
	define('AABS_ROMS', "*");

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
	define('AABS_SOURCE_CLOBBER', false);

/*
 * Count of concurrent jobs used to build the ROM and the patches
 * @default 16
 */
if (!defined('AABS_BUILD_JOBS'))
	define('AABS_BUILD_JOBS', 16);

/*
 * Indicates if uploads should be enabled
 * @default true
 */
if (!defined('AABS_UPLOAD'))
	define('AABS_UPLOAD', true);

/*
 * Type of upload-method ("ftp", "sftp" or "mega")
 * @default ftp
 */
if (!defined('AABS_UPLOAD_TYPE'))
	define('AABS_UPLOAD_TYPE', "ftp");

/*
 * Hostname or IP of the upload-server
 *
 * Not required for: local, mega
 *
 * @default "server.exmaple.com"
 */
if (!defined('AABS_UPLOAD_HOST'))
	define('AABS_UPLOAD_HOST', "server.exmaple.com");

/*
 * Port of the upload-server.
 *
 * Not required for: local, mega
 *
 * @default 22
 */
if (!defined('AABS_UPLOAD_PORT'))
	define('AABS_UPLOAD_PORT', 22);

/*
 * Username used to login to the upload-server
 *
 * Not required for: local
 *
 * ftp:  Username of the account
 * sftp: -||-
 * mega: -||-
 *
 * @default "root"
 */
if (!defined('AABS_UPLOAD_USER'))
	define('AABS_UPLOAD_USER', "root");

/*
 * Password used to login to the upload-server
 *
 * Not required for: local
 *
 * ftp:  Password of the account
 * sftp: -||-
 * mega: -||-
 *
 * @default "datpasswd"
 */
if (!defined('AABS_UPLOAD_PASS'))
	define('AABS_UPLOAD_PASS', "datpasswd");

/*
 * Directory to which the builds should be uploaded to
 * @default "/var/www/https/build/%Y-%m-%d_%H%i/{SHORT_DEVICE}"
 */
if (!defined('AABS_UPLOAD_DIR'))
	define('AABS_UPLOAD_DIR', "/var/www/https/build/{SHORT_DEVICE}/%Y-%m-%d_%H%i");

/*
 * New name of the uploaded build
 * @default "{ROM}-multitarget-%Y-%m-%d_%H%i.zip"
 */
if (!defined('AABS_UPLOAD_FILE'))
	define('AABS_UPLOAD_FILE', "{TYPE-}{ROM}-{PROP:ro.build.version.release}-{DEVICE}-%Y-%m-%d_%H%i.{TYPE_FILEEXT}");

/*
 * Comma-separated list of hashes to be generated
 * @default "md5, sha1, sha256"
 */
if (!defined('AABS_HASH_METHODS'))
	define('AABS_HASH_METHODS', "md5, sha1, sha256");
