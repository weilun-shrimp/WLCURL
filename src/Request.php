<?php

namespace Weilun\WLCURL;

use CurlHandle;

class Request
{
    public Curl $curl;

    public string $baseUrl;
    public string $path;
    public array $headers;
    public array | string $body;
    public string $query;
    public array $opts;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
        $this->baseUrl();
    }

    protected function baseUrl()
    {
        $this->baseUrl = $this->curl->baseUrl;
    }

    protected function path()
    {
        $this->path = $this->curl->path;
    }

    protected function headers()
    {
        foreach ($this->curl->headers as $key => $value) {
            $this->headers[] = $key . ': ' . $value;
        }
    }

    protected function body()
    {
        $this->body = $this->curl->body;
    }
}
