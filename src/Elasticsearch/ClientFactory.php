<?php


namespace Lenvendo\Elasticsearch;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ClientFactory
{
    public function create(string $host): Client
    {
        return ClientBuilder::create()->setHosts([$host])->build();
    }
}