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
	local name=${1:1}
	local name=$(echo ${name} | sed -r 's/[\-]+/_/g');
	local local_name="__${name}"

	eval "export ${local_name}=()"
	export aabs_current_macro=${local_name}
}

function macro_end_parse {
	export aabs_current_macro=""
}

function macro_add_line {
	if [ "${aabs_current_macro}" == "" ]; then
		return 1
	fi

	local macro=$aabs_current_macro
	local value=${1:1}
	eval "export ${macro}+=('${value}')"
}

function macro_run {
	local name=${1:1}
	local name=$(echo ${name} | sed -r 's/[\-]+/_/g');
	local local_name="__${name}"
	local content=${!local_name}

	# parse the project list
	aabs_parse_list $file macro_project_list

	for macro_rproject in "${macro_project_list[@]}"; do

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
			command_parse "$rproject"

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
