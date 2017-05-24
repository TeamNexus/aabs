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
	local source_dir="${__rom_source}/out/target/product/${__codename}"
	local tmp_upload_base="${__copy_path##*/}"
	local tmp_upload_path="${upload_basedir}/${__copy_path%/*}/.${tmp_upload_base%.*}.${tmp_upload_base##*.}"
	local upload_path="${upload_basedir}/${__copy_path}"
	local output_artifact_basename=$(basename ${source_dir}/${__output_expr})
	local output_artifact="${source_dir}/${output_artifact_basename}"

	# create SFTP batch file
	local batch_file=$(mktemp)
	echo "cd $(dirname ${tmp_upload_path})" >> $batch_file
	__assert__ $?
	echo "put ${output_artifact} ${tmp_upload_path}" >> $batch_file
	__assert__ $?
	echo "rename ${tmp_upload_path} ${upload_path}" >> $batch_file
	__assert__ $?
	echo "exit" >> $batch_file
	__assert__ $?

	# remote: setup paths and upload
	sshpass -p "${upload_pass}" ssh $upload_user@$upload_host "mkdir -p $(dirname ${tmp_upload_path})"
	__assert__ $?
	sshpass -p "${upload_pass}" sftp -P$upload_port -oBatchMode=no -b$batch_file $upload_user@$upload_host
	__assert__ $?

	# clean up
	rm $batch_file
	__assert__ $?
}

function copy_build {
	local source_dir="${__rom_source}/out/target/product/${__codename}"
	local copy_dir="${copy_basedir}/$(dirname ${__copy_path})"
	local copy_path="${copy_basedir}/${__copy_path}"
	local output_artifcat="${source_dir}/$(basename ${source_dir}/${__output_expr})"

	mkdir -p "$copy_dir}"
	__assert__ $?

	cp "${output_artifcat}" "${copy_path}"
	__assert__ $?
}

function start_build {
	local source_dir="${__rom_source}"

	cd $source_dir
	__assert__ $?

	# prepare build
	. build/envsetup.sh
	__assert__ $?
	lunch ${__lunch_combo}
	__assert__ $?

	# clean if required
	if [ "$__clobber" == "true" ]; then
		make clobber
		__assert__ $?
	fi

	# clean output every time
	rm "${source_dir}/${__output_expr}"

	# build
	make ${__make_command}
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
