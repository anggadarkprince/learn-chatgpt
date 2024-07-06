<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Query;
use Psr\Http\Message\RequestInterface;

class NewsApi
{
    private $client;

    public function __construct()
    {
        $apiKey = $_ENV['NEWSAPI_API_KEY'];

        $addQueryParamMiddleware = Middleware::mapRequest(function (RequestInterface $request) use ($apiKey) {
            $uri = $request->getUri();
            $queryParams = Query::parse($uri->getQuery());

            // Add your default query parameter here
            $queryParams['apiKey'] = $apiKey;

            // Build the query string again
            $newQuery = Query::build($queryParams);
            $newUri = $uri->withQuery($newQuery);

            // Return the modified request
            return $request->withUri($newUri);
        });

        $stack = HandlerStack::create(); // Create a handler stack
        $stack->push($addQueryParamMiddleware); // Add the middleware
        $this->client = new Client([
            'base_uri' => 'https://newsapi.org/v2/',
            'handler' => $stack,
        ]);
    }

    public function search($query)
    {
        $response = $this->client->get('everything', [
            'query' => [
                'q' => $query,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

}