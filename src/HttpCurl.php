<?php

namespace Weilun\WLCURL;

use WeiLun\WLCURL\Curl;

class HttpCurl extends Curl
{
    public string $method = 'GET';
    public array $body = [];
    /**
     * All key value type are constrained to be string.
     */
    protected array $_headers = [];

    public function __construct()
    {
        $this->_opts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        switch ($name) {
                // custom method
            case 'request':
            case 'Request':
            case 'REQUEST':
                if (count($arguments) < 1)
                    throw new \Exception("Missing the method parameter in $name static function.");
                if (!is_string($arguments[0]) or !$arguments[0])
                    throw new \Exception("The $name static function's method parameter is required to be a string and no empty.");
                $static = new static;
                $static->method = $arguments[0];
                return $static;

                // general methods
            case 'get':
            case 'Get':
            case 'GET':
            case 'post':
            case 'Post':
            case 'POST':
            case 'put':
            case 'Put':
            case 'PUT':
            case 'patch':
            case 'Patch':
            case 'PATCH':
            case 'delete':
            case 'Delete':
            case 'DELETE':
                $static = new static;
                $static->method = strtoupper($name);
                return $static;

            default:
                throw new \Exception("You have called the unsupport method $name in " . self::class);
        }
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'headers':
                return $this->_headers;
                break;
            case 'opts':
                return $this->_opts;
                break;
            default:
                return null;
        }
    }

    public function __set(string $name, mixed $arguments)
    {
        switch ($name) {
            case 'headers':
                $this->headers($arguments);
                break;
            case 'opts':
                $this->opts($arguments);
                break;
        }
    }

    public function method(string $method)
    {
        $this->path = $method;
        return $this;
    }

    public function test(string $mode = '')
    {
        if (!$mode) $mode = static::$defaultMode;
        return $mode;
    }
}
