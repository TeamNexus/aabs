<?php

if (function_exists("eio_dup2")) {
	rmkdir(dirname(AABS_LOG));
	$logfile = fopen(AABS_LOG, "w+");
	eio_dup2(STDOUT, $logfile);
}
