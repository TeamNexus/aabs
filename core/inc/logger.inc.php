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

if (function_exists("eio_dup2")) {
	rmkdir(dirname(AABS_LOG));
	$logfile = fopen(AABS_LOG, "w+");	
	eio_dup2($logfile, STDOUT, EIO_PRI_MAX);
	eio_event_loop();
}
