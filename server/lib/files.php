<?php

function listFiles($dir){
    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $path = $dir . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($path)) {
                    $files = array_merge($files, listFiles($path));
                } else {
                    $files[] = $path;
                }
            }
        }
        closedir($handle);
    }
    return $files;
}