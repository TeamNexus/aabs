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

function command_validate_command {
	local valid_commands=(
		# Internal commands
		'pre-build'
		'post-build'

		# OS-commands
		'repo'
		'git'
		'make'
		'cp'
		'mv'
		'rm'
		'touch '
		'mkdir'
		'zip'
		'unzip'
		'sed'
	)


	for i in "${valid_commands[@]}"
	do
		if [ "${i}" == "${command}" ] ; then
			return 0
		fi
	done

	echo "Invalid command \"${name}\" found while parsing project-list"
	return 1
}

function command_parse {
	local raw_arguments=( $1 )
	local command=${raw_arguments[0]:1}
	local arguments=""

	command_validate_command $command
	__assert__ $?

	for (( i = 1; i < ${#raw_arguments[@]}; i++ )); do
		local arguments="${arguments}${raw_arguments[$i]} "
	done

	# expand all registered variables
	for i in "${aabs_variables[@]}"
	do
		local name=$(echo ${i} | sed -r 's#[\-]+#_#g');
		local local_name="__${name}"
		local arguments=$(echo "${arguments}" | sed "s/\${${i}}/$(echo ${!local_name} | sed -e 's/[\/&]/\\&/g')/g")
	done

	export aabs_command=${command}
	export aabs_arguments=${arguments}

	return 0
}

function command_run {
	local source_dir="${__rom_source}"
	local command=${1}

	cd $source_dir
	__assert__ $?

	# run command
	echo "command_run: $(which ${command}) $aabs_arguments"
	$(which ${command}) $aabs_arguments
	__assert__ $?
}

function command_run_repo {
	local source_dir="${__rom_source}"

	cd $source_dir
	__assert__ $?

	# run repo
	echo " > $AABS_BIN_REPO $aabs_arguments"
	$AABS_BIN_REPO $aabs_arguments
	__assert__ $?
}

function command_run_post_build {
	# Build finished, copy/upload if enabled
	# --------------------------------------
	# I know it's not the correct way, but
	# we can be sure the variable is empty or set
	if [[ "${copy_basedir}" != "" ]]; then
		__codename="$1" \
		__output_expr="$2" \
			copy_build
	fi
	if [[ "${upload_host}" != "" ]]; then
		__codename="$1" \
		__output_expr="$2" \
			upload_build
	fi
}
