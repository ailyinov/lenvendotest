<?php


namespace Lenvendo\Service\Bookmark;


class HttpClient
{
    public function request(string $url): string
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);

        return (string) $response->getBody();
    }
}