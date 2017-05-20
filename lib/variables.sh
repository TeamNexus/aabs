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

function aabs_variable_validate {
	name=$1

	valid_commands=(
		# Global Settings
		'$upload-host'
		'$upload-port'
		'$upload-user'
		'$upload-pass'
		'$upload-basedir'
		'$copy-basedir'

		# Global Variables
		'$category'
		'$codename'
		'$model'
		'$rom-name'
		'$rom-source'
		'$lunch-combo'
		'$output-expr'
		'$copy-path'
		'$clobber'
		'$concr-jobs'
	)

	for i in "${valid_commands[@]}"
	do
		if [ "${i}" == "${name}" ] ; then
			return 1
		fi
	done

	return 0
}

function aabs_parse_variable {
	name=$1
	value=$2

	aabs_variable_validate ${name}
	ret=$?

	if [ $ret -eq 1 ]; then
		# parse variable on if possible
		name="${name:1:${#name}-1}"

		# make a usable name
		name=$(echo ${name} | sed -r 's/[\-]+/_/g');
		local_name="__${name}"

		export $name=${value}
		export $local_name=${value}

		return 1
	fi

	return 0
}

function aabs_get_value {
	name=$1
	value=$2

	name=$(echo ${name} | sed -r 's/[\-]+/_/g');

	if [ "${value}" == "-" ]; then
		value=${!name} # use global value
	fi

	local_name="__${name}"
	export $local_name=${value}
}

function aabs_expand_variable {
	name=$(echo ${1} | sed -r 's/[\-]+/_/g');
	varname=$(echo ${2} | sed -r 's/[\-]+/_/g');
	real_varname=$2;

	local_name="__${name}"
	local_varname="__${varname}"

	export $local_name=$(echo "${!local_name}" | sed "s/\${${real_varname}}/${!local_varname}/g")
}
