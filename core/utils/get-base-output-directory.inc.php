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

function get_base_output_directory($rom, $source_dir) {
	$common_out_dir = getenv("OUT_DIR_COMMON_BASE");
	if ($common_out_dir === false) {
		return "{$source_dir}/out";
	} else {
		return "${common_out_dir}/{$rom}";
	}
}
