<?php

use GuzzleHttp\Client;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['OCRSPACE_API_KEY'];
$client = new Client([
    'base_uri' => 'https://api.ocr.space/',
    'headers' => [
        'Content-Type' => 'application/json',
        'apiKey' => 'Bearer ' . $apiKey,
    ]
]);

$targetFile = 'statics/packing-list-scanned.pdf';
$fileData = fopen($targetFile, 'r');

// https://ocr.space/OCRAPI
$response = $client->post('parse/image', [
    'multipart' => [
        ['name' => 'file', 'contents' => $fileData], // url or base64Image
        ['name' => 'language', 'contents' => 'eng'],
        ['name' => 'filetype', 'contents' => 'pdf'],
        ['name' => 'detectOrientation', 'contents' => 'true'],
        ['name' => 'isTable', 'contents' => 'true'],
        //['name' => 'OCREngine', 'contents' => '1'],
    ]
]);
$response =  json_decode($response->getBody(),true);

?>
<pre>
    <?php foreach (($response['ParsedResults'] ?? []) as $result): ?>
        <?= print_r($result['ParsedText'] ?? '', true) ?>
    <?php endforeach; ?>
</pre>