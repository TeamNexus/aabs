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
