<?php

namespace WeiLun\WLCURL;

class Builder
{
    protected Curl | null $curl = null;

    protected string $_scheme = '';
    protected string $_user = '';
    protected string $_pass = '';
    protected string $_host = '';
    protected int $_port = 0;
    protected string $_path = '';
    protected string $_fragment = '';
    protected array $_queries = [];

    protected array $_opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ];

    public function __construct() {}

    public function __get(string $name)
    {
        switch ($name) {
            case 'scheme':
            case 'user':
            case 'pass':
            case 'host':
            case 'port':
            case 'path':
            case 'fragment':
            case 'queries':
            case 'opts':
                return $this->{"_$name"};
                break;
            default:
                return null;
        }
    }

    public function __set(string $name, mixed $argument)
    {
        switch ($name) {
            case 'scheme':
            case 'user':
            case 'pass':
            case 'host':
            case 'port':
            case 'path':
            case 'fragment':
                $this->{$name}($argument);
                break;
            case 'queries':
            case 'opts':
                if (!is_array($argument))
                    throw new \Exception("The $name parameter of " . static::class . ' is required to be an array.');
                $this->{$name}($argument);
                break;
        }
    }

    public function url(string $url)
    {
        $decodeUrl = parse_url($url);
        if (!$decodeUrl) throw new \Exception('
            Invalid $url argument value be found in ' . static::class . '::' . __FUNCTION__ . '(). 
            The invalid $url argument => ' . $url . '
        ');

        foreach (['scheme', 'host', 'port', 'user', 'pass', 'path', 'fragment'] as $v) {
            if (isset($decodeUrl[$v]))
                $this->{$v}($decodeUrl[$v]);
        }

        if (isset($decodeUrl['query']) and $decodeUrl['query']) {
            parse_str($decodeUrl['query'], $output);
            $this->queries($output);
        }

        return $this;
    }

    /**
     * @throws \Exception - if there is got invalid $scheme argument, eg. contains white space.
     */
    public function scheme(string $scheme)
    {
        $scheme = trim($scheme);
        if (str_contains($scheme, ' ')) throw new \Exception('
            Invalid $scheme argument value be found in ' . static::class . '::' . __FUNCTION__ . '(). 
            The $scheme argument is not allowed to contains white space.
            The invalid $scheme argument => ' . $scheme . '
        ');
        $this->scheme = $scheme;
        return $this;
    }

    public function user(string $user)
    {
        $this->user = rawurldecode($user);
        return $this;
    }

    public function pass(string $pass)
    {
        $this->pass = rawurldecode($pass);
        return $this;
    }

    public function host(string $host)
    {
        $this->host = rawurldecode($host);
        return $this;
    }

    /**
     * @throws \Exception - If there got an invalid $port argument. eg. less than zero or greater than 65535.
     */
    public function port(int $port)
    {
        if ($port < 0 or $port > 65535) throw new \Exception('
            Invalid $port argument value be found in ' . static::class . '::' . __FUNCTION__ . '(). 
            The $port argument is not allowed to be less than zero or greater than 65535.
            The invalid $port argument => ' . $port . '
        ');
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
    public function path(string $raw_path, string $mode = 'replace')
    {
        $path = '';
        // decode raw_path
        $exploded = explode('/', $raw_path);
        foreach ($exploded as $k => $pathItem) {
            if ($k !== 0) $path .= '/';
            $path .= rawurldecode($pathItem);
        }
        if (substr($this->raw_path, -1) === '/')
            $path .= '/';

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
        $this->fragment = rawurldecode($fragment);
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

    public function buildUrl(): string
    {
        $url = "$this->scheme://";

        if ($this->user) {
            $url .= rawurlencode($this->user);
            if ($this->pass)
                $url .= ':' . rawurlencode($this->pass);
            $url .= '@';
        }

        $url .= rawurlencode($this->host);

        if ($this->port) $url .= ":$this->port";

        if ($this->path) {
            $exploded = explode('/', $this->path);
            foreach ($exploded as $k => $pathItem) {
                if ($k !== 0) $url .= '/';
                $url .= rawurlencode($pathItem);
            }
            if (substr($this->path, -1) === '/')
                $url .= '/';
        }

        if ($this->queries) $url .= '?' . http_build_query(
            data: $this->queries,
            encoding_type: PHP_QUERY_RFC3986
        );

        if ($this->fragment) $url .= '#' . rawurlencode($this->fragment);

        return $url;
    }

    public function buildOpts(): array
    {
        return $this->_opts + [CURLOPT_URL => $this->buildUrl()];
    }

    public function close()
    {
        if (!$this->curl or $this->curl->isClosed()) return;
        $this->curl->close();
    }

    public function exe() {}

    public function __destruct()
    {
        $this->close();
    }
}
