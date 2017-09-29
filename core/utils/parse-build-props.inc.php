<?php

function parse_build_props($build_prop) {
	$properties = array( );

	if (preg_match_all("/([a-zA-Z0-9\.\-\_]*)\=(.*)/", $build_prop, $prop_matches)) {
		$match_count = count($prop_matches[0]);
		for ($i = 0; $i < $match_count; $i++) {
			$key   = $prop_matches[1][$i];
			$value = $prop_matches[2][$i];

			$properties[$key] = $value;
		}
	}

	return $properties;
}
