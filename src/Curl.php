<?php

namespace WeiLun\WLCURL;

class Curl
{
    public string $scheme;
    public string $baseUrl;
    public string $path;
    public array $queries = [];

    protected array $_opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
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

    public function __set(string $name, mixed $arguments)
    {
        switch ($name) {
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
     * @param string $path - The value that will be replaced or be added after the $this->path variable.
     * @param string $mode 
     *  - Determind the $path argument will be replaced or be added after the $this->path variable.
     *  - Only accept the values in \Weilun\WLCURL\Constants::REPLACE_MODE, 'replace', \Weilun\WLCURL\Constants::ADD_MODE or 'add'. Otherwise will throw an exception.
     *  - \Weilun\WLCURL\Constants::REPLACE_MODE or 'replace' will replace the $this->path with $path argument.
     *  - \Weilun\WLCURL\Constants::ADD_MODE or 'add' will add the $path argument after the $this->path.
     *  - The default value is \Weilun\WLCURL\Constants::REPLACE_MODE.
     * @throws \Exception
     */
    public function path(string $path, string $mode = \Weilun\WLCURL\Constants::REPLACE_MODE)
    {
        switch ($mode) {
            case Constants::REPLACE_MODE:
                $this->path = $path;
                break;
            case Constants::ADD_MODE:
                $this->path .= $path;
                break;
            default:
                throw new \Exception('
                    Unsupport $mode argument value be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                    Only support in \Weilun\WLCURL\Constants::REPLACE_MODE or \Weilun\WLCURL\Constants::ADD_MODE.
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
     *  - Only accept the values in \Weilun\WLCURL\Constants::REPLACE_MODE, 'replace', \Weilun\WLCURL\Constants::ADD_MODE or 'add'. Otherwise will throw an exception.
     *  - \Weilun\WLCURL\Constants::REPLACE_MODE or 'replace' will replace the $this->queries with $queries argument.
     *  - \Weilun\WLCURL\Constants::ADD_MODE or 'add' will add the $queries argument after the $this->queries or replace a query if the key is already exists.
     *  - The default value is the self::REPLACE_MODE.
     * @throws \Exception
     */
    public function queries(array $queries, string $mode = \Weilun\WLCURL\Constants::REPLACE_MODE)
    {
        switch ($mode) {
            case \Weilun\WLCURL\Constants::REPLACE_MODE:
                $this->queries = $queries;
                break;
            case \Weilun\WLCURL\Constants::ADD_MODE:
                foreach ($queries as $k => $v) $this->query((string) $k, $v);
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . self::class . '::' . __FUNCTION__ . '(). 
                    Only support in \Weilun\WLCURL\Constants::REPLACE_MODE or \Weilun\WLCURL\Constants::ADD_MODE.
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
     *  - All keys and values are constrained to be string. Otherwise will throw an exception.
     * @param string $mode 
     *  - Determind the $headers argument will be replaced or be added of the $this->headers variable.
     *  - Only accept the values in \Weilun\WLCURL\Constants::REPLACE_MODE, 'replace', \Weilun\WLCURL\Constants::ADD_MODE or 'add'. Otherwise will throw an exception.
     *  - \Weilun\WLCURL\Constants::REPLACE_MODE or 'replace' will replace the $this->headers with $headers argument.
     *  - \Weilun\WLCURL\Constants::ADD_MODE or 'add' will add the $headers argument after the $this->headers or replace a header if the key is already exists.
     *  - The default value is the self::REPLACE_MODE.
     * @throws \Exception
     */
    public function headers(array $headers, string $mode = \Weilun\WLCURL\Constants::REPLACE_MODE)
    {
        foreach ($headers as $k => $v) {
            if (!is_string($k))
                throw new \Exception('The key of ' . static::class . '::' . __FUNCTION__ . '()\'s $headers argument must be a string.');
            if (!is_string($v))
                throw new \Exception('The value of ' . static::class . '::' . __FUNCTION__ . '()\'s $headers argument must be a string.');

            if ($mode != \Weilun\WLCURL\Constants::ADD_MODE) continue;
            $this->header($k, $v);
        }
        switch ($mode) {
            case \Weilun\WLCURL\Constants::REPLACE_MODE:
                $this->_headers = $headers;
                break;
            case \Weilun\WLCURL\Constants::ADD_MODE:
                // Do nothing. Becuase it is already inserted on the top of section.
                break;
            default:
                throw new \Exception(' 
                    Unsupport $mode argument be found in ' . static::class . '::' . __FUNCTION__ . '(). 
                    Only accept the values in \Weilun\WLCURL\Constants::REPLACE_MODE and \Weilun\WLCURL\Constants::ADD_MODE.
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
     * @param array $opts 
     *  - The value that will be replaced the whole $this->opts variable or added the key value of the $this->opts variable.
     *  - All keys are constrained to be integer. Otherwise will throw an exception.
     * @param string $mode 
     *  - Determind the $headers argument will be replaced or be added of the $this->headers variable.
     *  - Only accept the values in \Weilun\WLCURL\Constants::REPLACE_MODE, 'replace', \Weilun\WLCURL\Constants::ADD_MODE or 'add'. Otherwise will throw an exception.
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
     *  - Only accept the values in \Weilun\WLCURL\Constants::REPLACE_MODE, 'replace', \Weilun\WLCURL\Constants::ADD_MODE or 'add'. Otherwise will throw an exception.
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
