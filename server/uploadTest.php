<?php

require_once 'vendor/autoload.php';

use tuefekci\deta\Deta;

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

if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '');
	$dotenv->load();
} 

if($project_id == "" && isset($_ENV['DETA_PROJECT_ID'])) {
    $project_id = $_ENV['DETA_PROJECT_ID'];
}elseif($project_id == "" && isset($_ENV['DETA_SPACE_APP_INSTANCE_ID'])) {
    $project_id = $_ENV['DETA_SPACE_APP_INSTANCE_ID'];
}



$deta = new Deta();
$my_drive = $deta->drive('webroot');

$files = $my_drive->list();
if(!empty($files->names)) {
    $my_drive->delete($files->names);
} else {
    echo "No files to delete\n";
}

$dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'test';
$files = listFiles($dir);

foreach($files as $file) {
	$newPath = str_replace($dir.DIRECTORY_SEPARATOR, '', $file);
	$newPath = str_replace(DIRECTORY_SEPARATOR, '/', $newPath);
	// Upload a file
	$my_drive->put($newPath, file_get_contents($file));
    echo $file . "\n";
}
