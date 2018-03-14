 AABS
==========
Automated Android Build Script
----------

Dependencies
==========
Minimal requirements: (Sync and build)

  * Basic requirements for building AOSP
  * PHP (at least version 5.6)
  * PHP-functions `system' and `tempnam' enabled and no openbase_dir set

Recommended requirements: (Sync, build, patch and upload)

  * **Minimal requirements (Sync and build)**
  * And depending on the upload-target:
    * For MEGA: megacmd (+ available in PATH-variable)
	* For FTP: (PHP) ftp
	* For SFTP: (PHP) mcrypt (required for phpseclib)

Getting started
==========
Download AABS from the latest sources, copy the configuration
and set correct permissions for the main executable.

	git clone https://github.com/TeamNexus/aabs ./aabs/
	cp ./aabs/aabs.config.php ./aabs.config.php
	chmod +x ./aabs/aabs

If you want to, you can edit the configs and build-operations
to your needs. When you are finished, run "./aabs/aabs" and
all devices and models available on the TeamNexus-repo will
be built.

Command Line-Options
==========

	-s, --skip-sync        No synchronization of ROM-sources
	-b, --skip-build       Skips building the ROM
	-u, --skip-patch       Don't run let AABS-patcher run over the build
	-u, --skip-upload      Don't upload the ROM
	-d, --devices          Devices which should be built by AABS (Comma-separated, Have to be defined in aabs.build.php)
	-r, --roms             ROMS which should be built by AABS (Comma-separated, Have to be defined in aabs.build.php)

License
==========
Automated Android Build Script - Simple and automated Android Build-Script
Copyright (C) 2017-2018  Lukas Berger

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
