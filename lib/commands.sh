#
# Automated Android Build Script - Simple and automated Android Build-Script
# Copyright (C) 2017  Lukas Berger
# 
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#

# check if global indicator is set
if [ ! $AABS -eq 1 ]; then
	exit 1
fi

function command_parse {
	raw_arguments=( $1 )
	command=${raw_arguments[0]:1}
	arguments=""

	for (( i = 1; i < ${#raw_arguments[@]}; i++ )); do
		arguments="${arguments}${raw_arguments[$i]} "
	done

	export aabs_command=${command}
	export aabs_arguments=${arguments}
}

function command_run_repo {
	source_dir="${__rom_source}"
	cd $source_dir

	# run repo
	$AABS_BIN_REPO "$1"
	__assert__ $?
}

function command_run_git {
	source_dir="${__rom_source}"
	cd $source_dir

	# sync it
	$(which git) "$1"
	__assert__ $?
}
