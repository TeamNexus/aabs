<?php

function validate_rom($rom) {
	$sourcedir = AABS_SOURCE_BASEDIR . "/{$rom}";

	switch ($rom) {
		case "NexusOS":
		case "LineageOS":
		case "ResurrectionRemix":
		case "AOKP":
			return is_dir($sourcedir);
	}

	throw new Exception("Unsupported ROM: {$rom} (Supported: LineageOS, NexusOS, ResurrectionRemix, AOKP)");
}
