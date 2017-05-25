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

function macro_start_parse {
	if [ "${aabs_current_macro}" != "" ]; then
		return 1
	fi

	local name=${1}
	local name=$(echo ${name} | sed -r 's/[\-]+/_/g');
	local local_name="__aabs_macro_${name}"

	eval "export __aabs_macro_$name=\"\""
	export aabs_current_macro=$name
}

function macro_end_parse {
	if [ "${aabs_current_macro}" == "" ]; then
		return 1
	fi

	export aabs_current_macro=""
}

function macro_add_line {
	if [ "${aabs_current_macro}" == "" ]; then
		return 1
	fi

	local full_name="__aabs_macro_$aabs_current_macro"

	if [ "${!full_name}" == "" ]; then
		eval "export $full_name=\"$1\""
	else
		eval "export $full_name=\"${!full_name};$1\""
	fi
}

function macro_run {
	local name=$(echo ${1} | sed -r 's/[\-]+/_/g');
	local local_name="__aabs_macro_${name}"

	IFS=';' read -ra aabs_marco_run_list <<< $(echo ${!local_name})
	local list_items=${#aabs_marco_run_list[@]}

	for macro_rproject in "${aabs_marco_run_list[@]}"; do

		local macro_project=( $macro_rproject )

		aabs_parse_variable ${macro_project[0]} ${macro_project[1]}
		local ret=$?

		if [ $ret -eq 1 ]; then
			# was a variable, continue
			continue
		fi

		# check for the correct category
		if [ "$requested_category" != "*" ] && [ "$category" != "$requested_category" ]; then
			continue
		fi

		if [ "${macro_project[0]:0:1}" != "@" ]; then
			aabs_export_default_variables
		fi

		aabs_expand_default_variables

		# check for command
		if [ "${macro_project[0]:0:1}" == "@" ]; then
			# parse command
			command_parse "$macro_rproject"

			# run command, only allow specific ones
			case ${aabs_command} in
				# Internal commands
				post-build) command_run_post_build ;;

				# OS-commands
				repo) command_run_repo ;;
				*) command_run ${aabs_command} ;;
			esac
		else
			# start build
			start_build
		fi

	done
}
