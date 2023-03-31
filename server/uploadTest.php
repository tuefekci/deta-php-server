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
$project_id = "";

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
}


die();

$browser = new React\Http\Browser();

// wraps Browser in a Queue object that executes no more than 10 operations at once
$q = new Clue\React\Mq\Queue(50, null, function ($url, $body) use ($browser) {
    return $browser->post($url, array('X-Api-Key' => $_ENV['DETA_API_KEY']), json_encode($body));
});


$dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'test';
$files = listFiles($dir);
$baseUrl = "https://drive.deta.sh/v1/$project_id/webroot/";
foreach ($files as $file) {

	$newPath = str_replace($dir.DIRECTORY_SEPARATOR, '', $file);
	$newPath = str_replace(DIRECTORY_SEPARATOR, '/', $newPath);

    $url = $baseUrl."files?name={$newPath}";

    // Open the file for reading
    $fileOpen = fopen($file, 'r');

    // Create a ReadableStream from the file
    $fileStream = new ReadableResourceStream();
    $fileStream->emit('data', [$data = fread($fileOpen, filesize($file))]);
    $fileStream->close();



    $q($url, $fileStream)->then(function (Psr\Http\Message\ResponseInterface $response) use ($url, $file) {
        echo $url.PHP_EOL;
        echo $response->getBody();
    }, function (Exception $e) {
        echo 'Error: ' . $e->getMessage() . PHP_EOL;
    });
}
