<?php

use tuefekci\deta\Deta;

require_once 'vendor/autoload.php';
require_once 'lib/files.php';
require_once 'lib/nginxconf.php';

file_put_contents('/tmp/nginx.conf', generateNginxConf(getenv('PORT')));

if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '');
	$dotenv->load();
} 

$deta = new Deta();
$my_drive = $deta->drive('webroot');

$tmpFolder = '/tmp/webroot';
if (!file_exists($tmpFolder)) {
	mkdir($tmpFolder, 0777, true);
}

$files = $my_drive->list();

foreach($files->names as $file) {
	$dir = dirname($tmpFolder . DIRECTORY_SEPARATOR . $file);
	if (!file_exists($dir)) {
		mkdir($dir, 0777, true);
	}

	if(!file_exists($tmpFolder . DIRECTORY_SEPARATOR . $file)) {
		file_put_contents($tmpFolder . DIRECTORY_SEPARATOR . $file, $my_drive->get($file));
	}
}

/*
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'test';
$files = listFiles($dir);
foreach($files as $file) {
	$newPath = str_replace($dir, '', $file);
	// Upload a file
	$my_drive->put($newPath, file_get_contents($file));
    echo $file . "\n";
}
*/



// Upload a file
//$my_drive->put('file.txt', 'Hello, world!');