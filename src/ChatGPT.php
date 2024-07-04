<?php

namespace App;

use GuzzleHttp\Client;

class ChatGPT
{
    private $client;

    public function __construct()
    {
        $apiKey = $_ENV['OPENAPI_API_KEY'];
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ]
        ]);
    }

    public function ask($prompt, $options = [])
    {
        $response = $this->client->post('chat/completions', [
            'json' => [
                "model" => $options['model'] ?? "gpt-3.5-turbo",
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'stream' => $options['stream'] ?? false,
                'max_tokens' => $options['max_tokens'] ?? 4096
            ],
        ]);
        $result = json_decode($response->getBody(), true);

        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        } else {
            throw new \Exception("An error occurred.");
        }
    }
}