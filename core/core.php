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

$options = getopt("hsbpud:r:l:", array( "help", "skip-sync", "skip-build", "skip-patch", "skip-upload", "devices:", "roms:", "log:" ));
foreach ($options as $key => $value) {
	switch ($key) {
		case "h":
		case "help":
			goto help;
			break;

		case "s":
		case "skip-sync":
			if (defined("AABS_SKIP_SYNC"))
				goto invalid;
			if ($value !== false)
				goto invalid;

			define("AABS_SKIP_SYNC", true);
			break;

		case "b":
		case "skip-build":
			if (defined("AABS_SKIP_UPLOAD"))
				goto invalid;
			if ($value !== false)
				goto invalid;

			define("AABS_SKIP_BUILD", true);
			break;

		case "p":
		case "skip-patch":
			if (defined("AABS_SKIP_PATCH"))
				goto invalid;
			if ($value !== false)
				goto invalid;

			define("AABS_SKIP_PATCH", true);
			break;

		case "u":
		case "skip-upload":
			if (defined("AABS_SKIP_UPLOAD"))
				goto invalid;
			if ($value !== false)
				goto invalid;

			define("AABS_SKIP_UPLOAD", true);
			break;

		case "d":
		case "devices":
			if (defined("AABS_SKIP_SYNC"))
				goto invalid;
			if (!is_string($value) || empty($value))
				goto invalid;

			define("AABS_DEVICES", $value);
			break;

		case "r":
		case "roms":
			if (defined("AABS_ROMS"))
				goto invalid;
			if (!is_string($value) || empty($value))
				goto invalid;

			define("AABS_ROMS", $value);
			break;


		case "l":
		case "log":
			if (defined("AABS_LOG"))
				goto invalid;
			if (!is_string($value) || empty($value))
				goto invalid;

			define("AABS_LOG", $value);
			break;

		default:
invalid:
			echo "aabs: invalid option \"{$key}\"\n";
help:
			$argv0 = substr($argv[0], 0, strlen($argv[0]) - 4);
			echo "Usage: {$argv0} [options]\n";
			echo "Options:\n";
			echo "	-h, --help           Show this help-screen\n";
			echo "	-s, --skip-sync      No synchronization of ROM-sources\n";
			echo "	-b, --skip-build     Skips building the ROM\n";
			echo "	-u, --skip-patch     Don't run let AABS-patcher run over the build\n";
			echo "	-u, --skip-upload    Don't upload the ROM\n";
			echo "	-d, --devices        Devices which should be built by AABS (Comma-separated, Have to be defined in aabs.build.php)\n";
			echo "	-r, --roms           ROMS which should be built by AABS (Comma-separated, Have to be defined in aabs.build.php)\n";
			echo "	-l, --log            Redirect output to given file and console\n";
			echo "\n";
			exit;
	}
}

if (is_file(AABS_BASEDIR . "/../aabs.config.php"))
	include AABS_BASEDIR . "/../aabs.config.php";

include AABS_BASEDIR . "/aabs.config.php";

if (defined("AABS_LOG"))
	include AABS_BASEDIR . "/core/inc/logger.inc.php";

if (!defined("AABS_SKIP_SYNC"))
	define("AABS_SKIP_SYNC", false);

if (!defined("AABS_SKIP_BUILD"))
	define("AABS_SKIP_BUILD", false);

if (!defined("AABS_SKIP_PATCH"))
	define("AABS_SKIP_PATCH", false);

if (!defined("AABS_SKIP_UPLOAD"))
	define("AABS_SKIP_UPLOAD", false);

if (!defined("AABS_DEVICES"))
	define("AABS_DEVICES", "*");

if (!defined("AABS_ROMS"))
	define("AABS_ROMS", "*");

if (is_file(AABS_BASEDIR . "/../aabs.build.php"))
	include AABS_BASEDIR . "/../aabs.build.php";
else
	include AABS_BASEDIR . "/aabs.build.php";
