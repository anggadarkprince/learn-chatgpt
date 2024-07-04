<?php

require 'vendor/autoload.php';

use App\ChatGPT;
use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Client();

$company = $_POST['company'];

$response = $client->get('https://newsapi.org/v2/everything', [
    'query' => [
        'apiKey' => $_ENV['NEWSAPI_API_KEY'],
        'q' => $company,
    ],
]);

$data = json_decode($response->getBody(), true);

function extractNews($data)
{
    $news = "";

    if (isset($data['articles']) && is_array($data['articles'])) {
        foreach ($data['articles'] as $item) {
            if (isset($item['title']) && isset($item['description'])) {
                // array_push($news, array("title" =>$item['title'], "description" => $item['description'] ));
                $news_item = "
                   Title: {$item['title']}
                   Description: {$item['description']}
                ";
                $news = $news . " \n " . $news_item;
            }
        }
    } else {
        throw new \Exception("Invalid JSON response");
    }

    return $news;
}

$news = extractNews($data);


$promptTemplate = "
You will be given a list of news with title and description inside this delimiter ``` . 
Pick 5 news items from that. For each news in that 5, you need to analyze the sentiment and give out whether its:
Positive
Negative
Neutral

Here is the list of news:
``` {prompt} ```

Your answer should only have a JSON response like this:
[
    {
        title: Title,
        sentiment: Positive/Negative/Neutral
    },
    {
        title: Title,
        sentiment: Positive/Negative/Neutral
    },
]
";

function generatePrompt($prompt, $promptTemplate)
{
    return str_replace("{prompt}", $prompt, $promptTemplate);
}

$modifiedPrompt = generatePrompt($news, $promptTemplate);

$client = new ChatGPT();
$reply = $client->ask($modifiedPrompt);

echo $reply;
