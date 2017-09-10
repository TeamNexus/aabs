<?php

function upload_to_local($data) {
    $output = $data['output'];
    $hashes = $data['hashes'];
    $uploaddir = $data['upload']['dir'];
    $uploadfile = $data['upload']['file'];
    $uploadpath = "{$uploaddir}/{$uploadfile}";

    echo "Creating upload-directory...\n";
    __exec("mkdir -p \"{$uploaddir}\"");

    echo "Uploading build...\n";
    __exec("cp -p \"{$output}\" \"{$uploadpath}\"");

    foreach ($hashes as $hash => $file) {
        echo "Uploading {$hash}sum...\n";
        __exec("cp -p \"{$file}\" \"{$uploadpath}.{$hash}sum\"");
    }
}
