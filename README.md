AABS
==========
Automated Android Build Script
----------

Getting started
==========
Download the minimal aabs-autorun script from GitHub

	curl -L https://github.com/TeamNexus/aabs/raw/master/aabs-install > aabs-install
	chmod +x aabs-install

Then run aabs-install, the latest version of aabs is being downloaded
and launched.

AABS Project List Format
==========
```
  1   zerofltexx    SM-G920F   -     -     -     -     -     -     -;
 [0]      [1]          [2]    [3]   [4]   [5]   [6]   [7]   [8]   [9]

  AABS-specific:
    [0]: Category of this build, only built if the user wants to build this category; Can be any string/number

  Device-specific:
    [0]: Codename of the device (Variable: %codename%)
    [1]: Model of the device (%model%)

  ROM-specific:
    [2]: Name of the ROM (%rom-name%)
    [3]: Relative path at which the source is located (%rom-name%)
    [4]: Lunch-Combo used to build the ROM (%lunch-combo%)
    [5]: Expression used to find the finished build-artifact

  Generic:
    [6]: Relative path (directory and file) to which the artifact should be uploaded to (Available: %date%, %time%; self explaining)
    [7]: Indicates if "make clobber" should be ran before building the ROM
    [8]: Count of concurrect jobs per make-process
```

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
