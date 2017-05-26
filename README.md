AABS
==========
Automated Android Build Script
----------

Getting started
==========
Download the minimal aabs-autorun script from GitHub

	git clone https://github.com/TeamNexus/aabs ./aabs/
	cp ./aabs/aabs.config ./aabs.config
	chmod +x ./aabs/aabs

If you want to, you can edit the configs to your needs.
When you are finished, run "./aabs/aabs" and all devices
and models available on the TeamNexus-repo will be built.

Command Line-Options
==========

	-d|--devices		List of devices which should be built, seperated by a single space. Default: * to build all devices
	-r|--roms			List of ROMs which should be built, seperated by a single space. Default: * to build all ROMs
	-S|--skip-sync		Flag, which tells the build-system to skip "repo sync". Default: false
	-U|--skip-upload	Flag, which tells the build-system to skip the build-uploads. Default: false

License
==========
Automated Android Build Script - Simple and automated Android Build-Script
Copyright (C) 2017  Lukas Berger

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
