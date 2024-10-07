<?php

namespace Weilun\WLCURL\Http;

use WeiLun\WLCURL\Builder;

class HttpBuilder extends Builder
{
    public string $scheme = 'http';
    public string $method = 'GET';
    public array $body = [];
    /**
     * All the keys values type are constrained to be string.
     */
    protected array $_headers = [];

    protected array $_opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->opt(CURLOPT_ENCODING, '');
        $this->opt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
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
                    throw new \Exception("The $name static function's method parameter is required to be a string and none empty.");
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
        if ($parentValue = parent::__get($name))
            return $parentValue;
        switch ($name) {
            case 'headers':
                return $this->{"_$name"};
                break;
            default:
                return null;
        }
    }

    public function __set(string $name, mixed $argument)
    {
        parent::__set($name, $argument);
        switch ($name) {
            case 'headers':
                if (!is_array($argument))
                    throw new \Exception("The $name parameter of " . static::class . 'is required to be an array.');
                $this->{$name}($argument);
                break;
        }
    }

    /**
     * @param string $scheme - Only accept in 'http' or 'https'
     * @throws \Exception
     */
    public function scheme(string $scheme)
    {
        if ($scheme !== 'http' and $scheme !== 'https')
            throw new \Exception('
                Unsupport $scheme argument be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                Only accept in \'http\' or \'https\'.
            ');
        $this->scheme = $scheme;
        return $this;
    }

    public function method(string $method)
    {
        $this->method = $method;
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
     *  - All keys and values are constrained to be string. Otherwise will throw an exception.
     * @param string $mode 
     *  - Determind the $headers argument will be replaced or be added of the $this->headers variable.
     *  - Only accept the values in 'replace' or 'add'. Otherwise will throw an exception.
     *  - 'replace' will replace the $this->headers with $headers argument.
     *  - 'add' will add the $headers argument after the $this->headers or replace a header if the key is already exists.
     *  - The default value is 'replace'.
     * @throws \Exception
     */
    public function headers(array $headers, string $mode = 'replace')
    {
        foreach ($headers as $k => $v) {
            if (!is_string($k))
                throw new \Exception('The key of ' . static::class . '::' . __FUNCTION__ . '()\'s $headers argument must be a string.');
            if (!is_string($v))
                throw new \Exception('The value of ' . static::class . '::' . __FUNCTION__ . '()\'s $headers argument must be a string.');

            if ($mode != 'add') continue;
            $this->header($k, $v);
        }
        switch ($mode) {
            case 'replace':
                $this->_headers = $headers;
                break;
            case 'add':
                // Do nothing. Becuase it is already inserted on the top of section.
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                    Only accept the values in \'replace\' or \'add\'.
                ');
        }
        return $this;
    }

    /**
     * @param array $body - The value that will be replaced the whole $this->body variable or added the key value of the $this->body variable.
     * @param string $mode 
     *  - Determind the $body argument will be replaced or be added of the $this->body variable.
     *  - Only accept the values in 'replace' or 'add'. Otherwise will throw an exception.
     *  - 'replace' will replace the $this->body with $body argument.
     *  - 'add' will add the $body argument after the $this->body or replace a header if the key is already exists.
     *  - The default value is 'replace'.
     * @throws \Exception
     */
    public function body(array $body, string $mode = 'replace')
    {
        switch ($mode) {
            case 'replace':
                $this->body = $body;
                break;
            case 'add':
                foreach ($body as $k => $v) $this->body[$k] = $v;
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                    Only accept the values in \'replace\' or \'add\'.
                ');
        }
        return $this;
    }

    public function buildHeader(): array
    {
        return [];
    }

    public function buildOpts(): array
    {
        // $selfOpts = $this->_opts;
        // $selfOpts[CURLOPT_CUSTOMREQUEST] = $this->method;
        $parentOpts = parent::buildOpts();

        return $parentOpts +
            $this->_opts +
            [
                CURLOPT_CUSTOMREQUEST => $this->method,
                CURLOPT_HTTPHEADER => $this->buildHeader()
            ];
    }
}
