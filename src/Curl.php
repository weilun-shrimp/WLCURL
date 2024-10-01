<?php

namespace WeiLun\WLCURL;

class Curl
{
    public const REPLACE_MODE = 'Make function argument replace the $this variable.';
    public const ADD_MODE = 'Make function argument be added after the $this variable.';

    public string $baseUrl = '';
    public string $path = '';
    public array $query = [];
    public string $method = 'GET';
    public array $opt = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ];
    public array $body = [];
    /**
     * All key value type are constrained to be string.
     */
    protected array $_headers = [];


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
            default:
                return null;
        }
    }

    public function __set(string $name, array $arguments)
    {
        switch ($name) {
            case 'headers':
                $this->_headers = $arguments;
                break;
        }
    }

    public function baseUrl(string $url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * @param string $path - The value that will be replaced or added after the $this->path variable.
     * @param string $mode 
     *  - Determind the $path argument will be replaced or be added after the $this->path variable.
     *  - Only accept self::REPLACE_MODE or self::ADD_MODE value. Otherwise will throw an exception.
     *  - self::REPLACE_MODE will do the replace process.
     *  - self::ADD_MODE will do the added after process.
     *  - The default value is self::REPLACE_MODE.
     * @throws \Exception
     */
    public function path(string $path, string $mode = self::REPLACE_MODE)
    {
        switch ($mode) {
            case self::REPLACE_MODE:
                $this->path = $path;
                break;
            case self::ADD_MODE:
                $this->path .= $path;
                break;
            default:
                throw new \Exception('
                    Unsupport mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
                    Only support ' . self::class . '::REPLACE_MODE and ' . self::class . '::ADD_MODE.
                ');
        }
        return $this;
    }

    public function method(string $method)
    {
        $this->path = $method;
        return $this;
    }

    /**
     * @param array $query - The value that will be replaced the whole $this->query variable or added the key value of the $this->query variable.
     * @param string $mode 
     *  - Determind the $query argument will be replaced or be added of the $this->query variable.
     *  - Only accept self::REPLACE_MODE or self::ADD_MODE value. Otherwise will throw an exception.
     *  - self::REPLACE_MODE will do the whole replace process.
     *  - self::ADD_MODE will do the added after or replace key value if the key is the same process.
     *  - The default value is the self::REPLACE_MODE.
     * @throws \Exception
     */
    public function query(array $query, string $mode = self::REPLACE_MODE)
    {
        switch ($mode) {
            case self::REPLACE_MODE:
                $this->query = $query;
                break;
            case self::ADD_MODE:
                foreach ($query as $k => $v) $this->query[$k] = $v;
                break;
            default:
                throw new \Exception(' 
                    Unsupport mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
                    Only support ' . self::class . '::REPLACE_MODE and ' . self::class . '::ADD_MODE.
                ');
        }
        return $this;
    }

    /**
     * Set one header or replace a header if the key is exists.
     */
    public function header(string $key, string $value)
    {
        $this->_headers[$key] = $value;
        return $this;
    }

    /**
     * @param array $headers 
     *  - The value that will be replaced the whole $this->headers variable or added the key value of the $this->headers variable.
     *  - All key value are constrained to be string. Otherwise will throw an exception.
     * @param string $mode 
     *  - Determind the $headers argument will be replaced or be added of the $this->headers variable.
     *  - Only accept self::REPLACE_MODE or self::ADD_MODE value. Otherwise will throw an exception.
     *  - self::REPLACE_MODE will do the whole replace process.
     *  - self::ADD_MODE will do the added after or replace key value if the key is the same process.
     *  - The default value is the self::REPLACE_MODE.
     * @throws \Exception
     */
    public function headers(array $headers, string $mode = self::REPLACE_MODE)
    {
        foreach ($headers as $k => $v) {
            if (!is_string($k))
                throw new \Exception('The key of ' . self::class . '::' . __FUNCTION__ . '() $headers argument must be a string.');
            if (!is_string($v))
                throw new \Exception('The value of ' . self::class . '::' . __FUNCTION__ . '() $headers argument must be a string.');

            if ($mode !== self::ADD_MODE)
                continue;
            $this->query[$k] = $v;
        }
        switch ($mode) {
            case self::REPLACE_MODE:
                $this->_headers = $headers;
                break;
            case self::ADD_MODE:
                // Do nothing. Becuase it is already inserted on the top section.
                break;
            default:
                throw new \Exception(' 
                    Unsupport mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
                    Only support ' . self::class . '::REPLACE_MODE and ' . self::class . '::ADD_MODE.
                ');
        }
        return $this;
    }
}
