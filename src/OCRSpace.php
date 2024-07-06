<?php

namespace App;

use GuzzleHttp\Client;

class OCRSpace
{
    private $client;

    public function __construct()
    {
        $apiKey = $_ENV['OCRSPACE_API_KEY'];
        $this->client = new Client([
            'base_uri' => 'https://api.ocr.space/',
            'headers' => [
                'apiKey' => 'Bearer ' . $apiKey,
            ]
        ]);
    }

    public function parse($data)
    {
        // https://ocr.space/OCRAPI
        $multipart = [];
        foreach ($data as $key => $value) {
            $multipart[] = ['name' => $key, 'contents' => $value];
        }
        $response = $this->client->post('parse/image', [
            'multipart' => $multipart
        ]);
        $result = json_decode($response->getBody(),true);

        if (!$result || $result['IsErroredOnProcessing']) {
            throw new \Exception($result['ErrorMessage'][0] ?? 'Error occurred');
        }

        return $result;
    }
}