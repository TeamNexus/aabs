<?php

$options = getopt("hsbpud::r::", array( "help", "skip-sync", "skip-build", "skip-patch", "skip-upload", "devices::", "roms::" ));
foreach ($options as $key => $value) {
	switch ($key) {
		case "h":
		case "help":
			goto help;
			break;

		case "s":
		case "skip-sync":
			if (defined("AABS_SKIP_SYNC"))
				goto help;

			define("AABS_SKIP_SYNC", true);
			break;

		case "b":
		case "skip-build":
			if (defined("AABS_SKIP_UPLOAD"))
				goto help;

			define("AABS_SKIP_BUILD", true);
			break;

		case "p":
		case "skip-patch":
			if (defined("AABS_SKIP_PATCH"))
				goto help;

			define("AABS_SKIP_PATCH", true);
			break;

		case "u":
		case "skip-upload":
			if (defined("AABS_SKIP_UPLOAD"))
				goto help;

			define("AABS_SKIP_UPLOAD", true);
			break;

		case "d":
		case "devices":
			if (defined("AABS_SKIP_SYNC"))
				goto help;

			define("AABS_DEVICES", $value . " ");
			break;

		case "r":
		case "roms":
			if (defined("AABS_ROMS"))
				goto help;

			define("AABS_ROMS", $value . " ");
			break;
			
		default:
help:
			echo "aabs: invalid option \"{$key}\"\n";
			echo "Usage: aabs [options]\n";
			echo "Options:\n";
			echo "    -s, --skip-sync        No synchronization of ROM-sources\n";
			echo "    -b, --skip-build       Skips building the ROM\n";
			echo "    -u, --skip-patch       Don't run let AABS-patcher run over the build\n";
			echo "    -u, --skip-upload      Don't upload the ROM\n";
			echo "    -d, --devices          Devices which should be built by AABS (Comma-separated, Have to be defined in aabs.build.php)\n";
			echo "    -r, --roms             ROMS which should be built by AABS (Comma-separated, Have to be defined in aabs.build.php)\n";
			echo "\n";
			exit;
	}
}

if (is_file(dirname($argv[0]) . "/../aabs.config.php"))
	include dirname($argv[0]) . "/../aabs.config.php";

include dirname($argv[0]) . "/aabs.config.php";

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

if (is_file(dirname($argv[0]) . "/../aabs.build.php"))
	include dirname($argv[0]) . "/../aabs.build.php";
else
	include dirname($argv[0]) . "/aabs.build.php";
