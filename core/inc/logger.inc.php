<?php

if (function_exists("eio_dup2")) {
	$logfile = fopen(AABS_LOG, "w+");
	eio_dup2(STDOUT, $logfile);
}
