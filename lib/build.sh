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

function upload_build {
	_source_dir="${__rom_source}/out/target/product/${__codename}"
	_tmp_upload_path="${upload_basedir}/${__copy_path}"
	_upload_path="${upload_basedir}/.${__copy_path}"
	_output_artifcat="${_source_dir}/$(basename ${_source_dir}/${__output_expr})"

	# create SFTP batch file
	_batch_file=$(mktemp)
	echo "mkdir $(dirname ${_tmp_upload_path})" > $_batch_file
	__assert__ $?
	echo "cd $(dirname ${_tmp_upload_path})" >> $_batch_file
	__assert__ $?
	echo "put ${_output_artifcat} ${_tmp_upload_path}" >> $_batch_file
	__assert__ $?
	echo "rename ${_tmp_upload_path} ${_upload_path}" >> $_batch_file
	__assert__ $?
	echo "exit" >> $_batch_file
	__assert__ $?

	# remote: setup paths and upload
	sshpass -p "${upload_pass}" ssh $upload_user@$upload_host "mkdir -p $(dirname ${_tmp_upload_path})"
	__assert__ $?
	sshpass -p "${upload_pass}" sftp -P$upload_port -b $_batch_file $upload_user@$upload_host
	__assert__ $?

	# clean up
	rm $_batch_file
	__assert__ $?
}

function copy_build {
	_source_dir="${__rom_source}/out/target/product/${__codename}"
	_copy_dir="${copy_basedir}/$(dirname ${__copy_path})"
	_copy_path="${copy_basedir}/${__copy_path}"
	_output_artifcat="${_source_dir}/$(basename ${_source_dir}/${__output_expr})"

	mkdir -p "$_copy_dir}"
	__assert__ $?

	cp "${_output_artifcat}" "${_copy_path}"
	__assert__ $?
}

function start_build {
	_source_dir="${__rom_source}"

	cd $_source_dir
	__assert__ $?

	# prepare build
	. build/envsetup.sh
	__assert__ $?
	lunch ${__lunch_combo}
	__assert__ $?

	# clean if required
	if [ "$__clobber" == "true" ]; then
		make clobber -j${__concr_jobs}
		__assert__ $?
	fi

	# build
	make bacon -j${__concr_jobs}
	__assert__ $?

	# Build finished, copy/upload if enabled
	# --------------------------------------
	# I know it's not the correct way, but
	# we can be sure the variable is empty or set
	if [[ "${copy_basedir}" != "" ]]; then
		copy_build
	fi
	if [[ "${upload_host}" != "" ]]; then
		upload_build
	fi
}
