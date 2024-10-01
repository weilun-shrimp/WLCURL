<?php

namespace WeiLun\WLCURL;

class Curl
{
    public const REPLACE_MODE = 'Make function argument replace the $this variable.';
    public const ADD_MODE = 'Make function argument be added after the $this variable.';

    public string $baseUrl = '';
    public string $path = '';
    public array $queries = [];
    public string $method = 'GET';
    protected array $_opts = [
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

    public Request $request;

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
                    Unsupport $mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
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
     * Set one query or replace a query if the key is exists.
     */
    public function query(string $key, mixed $value)
    {
        $this->queries[$key] = $value;
        return $this;
    }

    /**
     * @param array $queries - The value that will be replaced the whole $this->queries variable or added the key value of the $this->queries variable.
     * @param string $mode 
     *  - Determind the $queries argument will be replaced or be added of the $this->queries variable.
     *  - Only accept self::REPLACE_MODE or self::ADD_MODE value. Otherwise will throw an exception.
     *  - self::REPLACE_MODE will do the whole replace process.
     *  - self::ADD_MODE will do the added or replace key value if the key is the same process.
     *  - The default value is the self::REPLACE_MODE.
     * @throws \Exception
     */
    public function queries(array $queries, string $mode = self::REPLACE_MODE)
    {
        switch ($mode) {
            case self::REPLACE_MODE:
                $this->queries = $queries;
                break;
            case self::ADD_MODE:
                foreach ($queries as $k => $v) $this->query((string) $k, $v);
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
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
     *  - self::ADD_MODE will do the added or replace key value if the key is the same process.
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

            if ($mode !== self::ADD_MODE) continue;
            $this->header($k, $v);
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
                    Unsupport $mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
                    Only support ' . self::class . '::REPLACE_MODE and ' . self::class . '::ADD_MODE.
                ');
        }
        return $this;
    }

    /**
     * Set one opt or replace a opt if the key is exists.
     */
    public function opt(int $key, mixed $value)
    {
        $this->_opts[$key] = $value;
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
     *  - self::ADD_MODE will do the added or replace key value if the key is the same process.
     *  - The default value is the self::REPLACE_MODE.
     * @throws \Exception
     */
    public function opts(array $opts, string $mode = self::REPLACE_MODE)
    {
        foreach ($opts as $k => $v) {
            if (!is_int($k))
                throw new \Exception('The key of ' . self::class . '::' . __FUNCTION__ . '() $opts argument must be an int.');

            if ($mode !== self::ADD_MODE) continue;
            $this->opt($k, $v);
        }
        switch ($mode) {
            case self::REPLACE_MODE:
                $this->_opts = $opts;
                break;
            case self::ADD_MODE:
                // Do nothing. Becuase it is already inserted on the top section.
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
                    Only support ' . self::class . '::REPLACE_MODE and ' . self::class . '::ADD_MODE.
                ');
        }
        return $this;
    }

    /**
     * @param array $body - The value that will be replaced the whole $this->body variable or added the key value of the $this->body variable.
     * @param string $mode 
     *  - Determind the $body argument will be replaced or be added of the $this->body variable.
     *  - Only accept self::REPLACE_MODE or self::ADD_MODE value. Otherwise will throw an exception.
     *  - self::REPLACE_MODE will do the whole replace process.
     *  - self::ADD_MODE will do the added or replace key value if the key is the same process.
     *  - The default value is the self::REPLACE_MODE.
     * @throws \Exception
     */
    public function body(array $body, string $mode = self::REPLACE_MODE)
    {
        switch ($mode) {
            case self::REPLACE_MODE:
                $this->body = $body;
                break;
            case self::ADD_MODE:
                foreach ($body as $k => $v) $this->body[$k] = $v;
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
                    Only support ' . self::class . '::REPLACE_MODE and ' . self::class . '::ADD_MODE.
                ');
        }
        return $this;
    }

    public function exe() {}
}
