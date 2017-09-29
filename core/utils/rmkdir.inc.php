<?php

function rmkdir($name) {
	if(!is_dir($name))
		xexec("mkdir -p {$name}");
}
