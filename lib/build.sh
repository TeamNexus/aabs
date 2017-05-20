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
	source_dir="${__rom_source}/out/target/product/${__codename}"
	upload_path="${upload_basedir}/${__copy_path}"
	output_artifcat="${source_dir}/$(basename ${source_dir}/${__output_expr})"

	# create SFTP batch file
	batch_file=$(mktemp)
	echo "mkdir $(dirname ${upload_path})" > $batch_file
	echo "cd $(dirname ${upload_path})" >> $batch_file
	echo "put ${output_artifcat} ${upload_path}" >> $batch_file
	echo "exit" >> $batch_file
	__assert__ $?

	# connect and upload
	sshpass -p "${upload_pass}" sftp -P$upload_port -b $batch_file $upload_user@$upload_host
	__assert__ $?

	# clean up
	rm $batch_filev
	__assert__ $?
}

function copy_build {
	source_dir="${__rom_source}/out/target/product/${__codename}"
	copy_dir="${copy_basedir}/$(dirname ${__copy_path})"
	copy_path="${copy_basedir}/${__copy_path}"
	output_artifcat="${source_dir}/$(basename ${source_dir}/${__output_expr})"

	mkdir -p "${copy_dir}"
	__assert__ $?

	cp "${output_artifcat}" "${copy_path}"
	__assert__ $?
}

function start_build {
	source_dir="${__rom_source}"

	cd $source_dir
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
	make otapackage -j${__concr_jobs}
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
