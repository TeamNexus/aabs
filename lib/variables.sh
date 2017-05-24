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
	local name=$1

	local valid_commands=(
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
		'$make-command'
		'$output-expr'
		'$copy-path'
		'$clobber'
	)

	for i in "${valid_commands[@]}"
	do
		if [ "${i}" == "${name}" ] ; then
			echo "Invalid variable \"${name}\" found while parsing project-list"
			return 1
		fi
	done

	return 0
}

function aabs_is_variable_registered {
	local e
	for e in "${aabs_variables[@]}"; do [[ "$e" == "$1" ]] && return 0; done
	return 1
}

function aabs_parse_variable {
	local name=$1
	local value=$2

	# aabs_variable_validate ${name}
	# ret=$?

	# if [ $ret -eq 1 ]; then
	if [ ${name:0:1} == '$' ]; then
		# parse variable on if possible
		local name="${name:1:${#name}-1}"

		# make a usable name
		local name=$(echo ${name} | sed -r 's/[\-]+/_/g');
		local local_name="__${name}"

		# parse the value
		if [[ "$value" == "{%tempfile%}" ]]; then
			local value=$(mktemp "${TMPDIR:-/tmp/}aabs-tempfile-XXXXXXXXXXXX")
		elif [[ "$value" == "{%tempdir%}" ]]; then
			local value=$(mktemp -d "${TMPDIR:-/tmp/}aabs-tempdir-XXXXXXXXXXXX")
		fi

		export $name="${name}"
		export $local_name="${value}"

		aabs_is_variable_registered "$name"
		if [ $? -eq 1 ]; then
			if [ -z $aabs_variables ]; then
				declare -ag aabs_variables=()
				aabs_variables=($name)
			else
				aabs_variables=("${aabs_variables[@]}" "$name")
			fi
		fi

		return 1
	fi

	return 0
}

function aabs_export_value {
	local name=$1
	local value=$2

	local name=$(echo ${name} | sed -r 's/[\-]+/_/g');

	if [ "${value}" == "-" ]; then
		local value=${!name} # use global value
	fi

	local local_name="__${name}"
	export $local_name=${value}
}

function aabs_expand_variable {
	local name=$(echo ${1} | sed -r 's/[\-]+/_/g');
	local varname=$(echo ${2} | sed -r 's/[\-]+/_/g');
	local real_varname=$2;

	local local_name="__${name}"
	local local_varname="__${varname}"

	export $local_name=$(echo "${!local_name}" | sed "s/\${${real_varname}}/${!local_varname}/g")
}

function aabs_export_default_variables {
	# [0] category
	# [1] codename
	# [2] model
	# [3] rom-name
	# [4] rom-source
	# [5] lunch-combo
	# [6] make-command
	# [7] output-expr
	# [8] copy-path
	# [9] upload-path
	# [10] clobber

	# prepare build-variables
	aabs_export_value "category"     ${project[0]}
	aabs_export_value "codename"     ${project[1]}
	aabs_export_value "model"        ${project[2]}
	aabs_export_value "rom-name"     ${project[3]}
	aabs_export_value "rom-source"   ${project[4]}
	aabs_export_value "lunch-combo"  ${project[5]}
	aabs_export_value "make-command" ${project[6]}
	aabs_export_value "output-expr"  ${project[7]}
	aabs_export_value "copy-path"    ${project[8]}
	aabs_export_value "upload-path"  ${project[9]}
	aabs_export_value "clobber"      ${project[10]}

	export __date=$(date +%Y-%m-%d)
	export __time=$(date +%H%M)
}

function aabs_expand_default_variables {
	# expand variables: category
	aabs_expand_variable "category" "codename"
	aabs_expand_variable "category" "model"
	aabs_expand_variable "category" "rom-name"

	# expand variables: rom-source
	aabs_expand_variable "rom-source" "codename"
	aabs_expand_variable "rom-source" "model"
	aabs_expand_variable "rom-source" "rom-name"

	# expand variables: lunch-combo
	aabs_expand_variable "lunch-combo" "codename"
	aabs_expand_variable "lunch-combo" "model"
	aabs_expand_variable "lunch-combo" "rom-name"

	# expand variables: upload-path
	aabs_expand_variable "upload-path" "codename"
	aabs_expand_variable "upload-path" "model"
	aabs_expand_variable "upload-path" "rom-name"
	aabs_expand_variable "upload-path" "date"
	aabs_expand_variable "upload-path" "time"

	# expand variables: copy-path
	aabs_expand_variable "copy-path" "codename"
	aabs_expand_variable "copy-path" "model"
	aabs_expand_variable "copy-path" "rom-name"
	aabs_expand_variable "copy-path" "date"
	aabs_expand_variable "copy-path" "time"
}
