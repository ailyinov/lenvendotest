<?php


namespace Lenvendo\Service\Bookmark;


class HttpClient
{
    public function request(string $url): string
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);

//        $response->getStatusCode(); // 200
//        $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'

        return (string) $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'
    }
}