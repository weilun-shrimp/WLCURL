<?php

namespace WeiLun\WLCURL;

use \Weilun\WLCURL\Constants;

class Curl
{
    public string $scheme = '';
    public string $user = '';
    public string $pass = '';
    public string $host = '';
    public int $port = 80;
    public string $path = '';
    public string $fragment = '';
    public array $queries = [];

    protected array $_opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ];

    public Request $request;

    public function __get(string $name)
    {
        switch ($name) {
            case 'opts':
                return $this->_opts;
                break;
            default:
                return null;
        }
    }

    public function __set(string $name, mixed $argument)
    {
        switch ($name) {
            case 'opts':
                if (!is_array($argument))
                    throw new \Exception("The $name parameter of " . static::class . 'is required to be an array.');
                $this->opts($argument);
                break;
        }
    }

    public function url(string $url)
    {
        $decodeUrl = parse_url($url);

        foreach (['scheme', 'host', 'port', 'user', 'pass', 'path', 'fragment'] as $v) {
            if (isset($decodeUrl[$v]))
                $this->{$v}($decodeUrl[$v]);
        }

        if (isset($decodeUrl['query']) and $decodeUrl['query']) {
            parse_str($decodeUrl['query'], $this->queries);
        }

        return $this;
    }

    public function scheme(string $scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    public function user(string $user)
    {
        $this->user = $user;
        return $this;
    }

    public function pass(string $pass)
    {
        $this->pass = $pass;
        return $this;
    }

    public function host(string $host)
    {
        $this->host = $host;
        return $this;
    }

    public function port(int $port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param string $path - The value that will be replaced or be added after the $this->path variable.
     * @param string $mode 
     *  - Determind the $path argument will be replaced or be added after the $this->path variable.
     *  - Only accept the values in 'replace' or 'add'. Otherwise will throw an exception.
     *  - 'replace' will replace the $this->path with $path argument.
     *  - 'add' will add the $path argument after the $this->path.
     *  - The default value is 'replace'.
     * @throws \Exception
     */
    public function path(string $path, string $mode = 'replace')
    {
        switch ($mode) {
            case 'replace':
                $this->path = $path;
                break;
            case 'add':
                $this->path .= $path;
                break;
            default:
                throw new \Exception('
                    Unsupport $mode argument value be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                    Only accept the values in \'replace\' or \'add\'.
                ');
        }
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
     * @param array $queries - The value that will be replaced the whole $this->queries variable or be added the key value of the $this->queries variable.
     * @param string $mode 
     *  - Determind the $queries argument will be replaced or be added of the $this->queries variable.
     *  - Only accept the values in 'replace' or 'add'. Otherwise will throw an exception.
     *  - 'replace' will replace the $this->queries with $queries argument.
     *  - 'add' will add the $queries argument after the $this->queries or replace a query if the key is already exists.
     *  - The default value is 'replace'.
     * @throws \Exception
     */
    public function queries(array $queries, string $mode = 'replace')
    {
        switch ($mode) {
            case 'replace':
                $this->queries = $queries;
                break;
            case 'add':
                foreach ($queries as $k => $v) $this->query((string) $k, $v);
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                    Only accept the values in \'replace\' or \'add\'.
                ');
        }
        return $this;
    }

    public function fragment(string $fragment)
    {
        $this->fragment = $fragment;
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
     * @param array $opts 
     *  - The value that will be replaced the whole $this->opts variable or added the key value of the $this->opts variable.
     *  - All keys are constrained to be integer. Otherwise will throw an exception.
     * @param string $mode 
     *  - Determind the $opts argument will be replaced or be added of the $this->opts variable.
     *  - Only accept the values in 'replace' or 'add'. Otherwise will throw an exception.
     *  - 'replace' will replace the $this->opts with $opts argument.
     *  - 'add' will add the $opts argument after the $this->opts or replace a query if the key is already exists.
     *  - The default value is 'replace'.
     * @throws \Exception
     */
    public function opts(array $opts, string $mode = 'replace')
    {
        foreach ($opts as $k => $v) {
            if (!is_int($k))
                throw new \Exception('The key of ' . static::class . '::' . __FUNCTION__ . '()\'s $opts argument must be an int.');

            if ($mode !== 'add') continue;
            $this->opt($k, $v);
        }
        switch ($mode) {
            case 'replace':
                $this->_opts = $opts;
                break;
            case 'add':
                // Do nothing. Becuase it is already inserted on the top section.
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                    Only accept the values in \'replace\' or \'add\'.
                ');
        }
        return $this;
    }

    public function exe() {}
}
