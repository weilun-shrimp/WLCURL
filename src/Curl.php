<?php

namespace WeiLun\WLCURL;

use CurlHandle;

class Curl
{
    protected Builder $builder;
    protected CurlHandle $handle;
    protected bool $isClosed = false;

    public string $url = '';
    public array $opts = [];

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->handle = curl_init();
    }

    public function initFromBuilder()
    {
        $this->url = $this->builder->buildUrl();
        $this->opts = $this->builder->buildOpts();
    }

    public function close()
    {
        if ($this->isClosed) return;
        curl_close($this->handle);
        $this->isClosed = true;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function __destruct()
    {
        if ($this->isClosed()) return;
        $this->close();
    }
}
