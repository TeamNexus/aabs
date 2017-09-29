<?php

$__utils_dir = dirname($argv[0]) . '/core/utils/';
$__utils = scandir($__utils_dir);

foreach ($__utils as $util) {
	if ($util[0] == '.') {
		continue;
	}
	require_once $__utils_dir . $util;
}
