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
	source_dir="${AABS_SOURCE_BASEDIR}/${__rom_source}/out/target/product/${__codename}"
	upload_path="${AABS_UPLOAD_BASEDIR}/${__upload_path}"
	output_artifcat="${source_dir}/$(basename ${source_dir}/${__output_expr})"

	# create SFTP batch file
	batch_file=$(mktemp)
	echo "mkdir $(dirname ${upload_path})" > $batch_file
	echo "cd $(dirname ${upload_path})" >> $batch_file
	echo "put ${output_artifcat} ${upload_path}" >> $batch_file
	echo "exit" >> $batch_file

	# connect and upload
	sshpass -p "${AABS_UPLOAD_PASS}" sftp -P$AABS_UPLOAD_PORT -b $batch_file $AABS_UPLOAD_USER@$AABS_UPLOAD_HOST

	# clean up
	rm $batch_file
}

function start_build {
	source_dir="${AABS_SOURCE_BASEDIR}/${__rom_source}"

	cd $source_dir

	# prepare build
	. source/build
	lunch ${__lunch_combo}

	# clean if required
	if [ $__clobber == "true" ]; then
		make clobber -j${__concr_jobs}
	fi

	# build
	make otapackage -j${__concr_jobs}

	# Build finished, upload if enabled
	if [[ $AABS_UPLOAD_HOST && ${AABS_UPLOAD_HOST-x} ]]; then
		upload_build
	fi
}