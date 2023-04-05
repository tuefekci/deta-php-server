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
    echo "Loading .env file\n".realpath(__DIR__ . DIRECTORY_SEPARATOR . '..')."\n";
	$dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
	$dotenv->load();

    if(empty($_ENV)) {
        $_ENV = getenv();
    }

} 

$project_id = "";
if($project_id == "" && isset($_ENV['DETA_PROJECT_ID'])) {
    $project_id = $_ENV['DETA_PROJECT_ID'];
}elseif($project_id == "" && isset($_ENV['DETA_SPACE_APP_INSTANCE_ID'])) {
    $project_id = $_ENV['DETA_SPACE_APP_INSTANCE_ID'];
} else {
    echo "No project id found\n";
    exit;
}



$deta = new Deta();
$my_drive = $deta->drive('public');

$files = $my_drive->list();
if(!empty($files->names)) {
    $my_drive->delete($files->names);
} else {
    echo "No files to delete\n";
}

$dir = __DIR__ . DIRECTORY_SEPARATOR  . 'public';
$files = listFiles($dir);

foreach($files as $file) {
	$newPath = str_replace($dir.DIRECTORY_SEPARATOR, '', $file);
	$newPath = str_replace(DIRECTORY_SEPARATOR, '/', $newPath);
	// Upload a file
	$my_drive->put($newPath, file_get_contents($file));
    echo $file . "\n";
}
