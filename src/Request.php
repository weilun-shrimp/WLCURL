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

    protected function query()
    {
        $result = [];
        foreach ($this->curl->queries as $k => $v) {
            if (is_array($v)) {
                $result[] = $k . $this->tailQuery($v);
                continue;
            }
            $result[] = "$k=$v";
        }
        $this->query = implode('&', $result);
    }

    protected function tailQuery(array $tailQuery): array
    {
        $result = [];
        foreach ($tailQuery as $k => $v) {
            if (is_array($v)) {
                $result[] = "[$k]" . $this->tailQuery($v);
                continue;
            }
            $result[] = "[$k]=$v";
        }
        return $result;
    }
}
