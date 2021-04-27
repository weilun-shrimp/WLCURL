<?php

namespace App\Models\WLCURL;

class WLCURL
{
    public $curl;

    public $base_url;
    public $end_point;
    public $url_para = [];
    public $method = 'GET';
    public $opt = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ];
    public $post_field = [];

    public $header = [];
    protected $token_type = 'Bearer';
    protected $token;

    /**
     * for api request
     */
    protected $para_type = 'http';
    protected $encode_flag = JSON_UNESCAPED_UNICODE;
    protected $encode_depth = 512;

    protected $query_header = [];
    protected $query_url;
    protected $query_end_point;
    protected $query_url_para;
    protected $query_post_field;
    public $result;

    //for check obj para
    protected $check_method = ['GET', 'POST', 'PUT', 'DELETE'];
    protected $check_para_type = ['http', 'json'];

    public function __construct($method = 'GET')
    {
        $this->method = strtoupper($method);
        $this->opt[CURLOPT_CUSTOMREQUEST] = $this->method;
        $this->check_method();
        if (!empty(env('WLCURL_BASE_URL'))) {
            $this->base_url = env('WLCURL_BASE_URL');
        }

        if (!empty(env('WLCURL_DEFULT_TOKEN_TYPE'))) {
            $this->token_type = env('WLCURL_DEFULT_TOKEN_TYPE');
        }

        if (!empty(env('WLCURL_DEFULT_TOKEN'))) {
            $this->token = env('WLCURL_DEFULT_TOKEN');
        }
    }

    protected function check_method()
    {
        try {
            if (!in_array(strtoupper($this->method), $this->check_method)) {
                throw new \Exception('CURL method error,only accept [' . implode(', ', $this->check_method) . '], please check and try again.');
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    protected function check_para_type()
    {
        try {
            if (!in_array($this->para_type, $this->check_para_type)) {
                throw new \Exception('CURL query type error,only accept [' . implode(', ', $this->check_para_type) . '], please check and try again.');
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    protected function check_http_query_para($para)
    {
        try {
            if (!is_array($para)) {
                throw new \Exception('CURL query para error, url or post field, type must be array, please check and try again.');
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    protected function chaeck_encode()
    {
        try {
            if (!is_numeric($this->encode_depth)) {
                throw new \Exception('CURL encode depth error, type must be numeric, please check and try again.');
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    public function exe($decode = true, $type = false)
    {
        //check first
        $this->check_method();
        $this->check_para_type();
        $this->check_url_para($this->url_para);
        $this->check_url_para($this->post_field);

        $this->curl = curl_init(); // init curl
        $this->build_end_point();
        $this->build_url_para();
        $this->build_url();
        $this->build_token(); // must build before build header, because token need set in header
        $this->build_header(); // prepare header and set header and prepare to opt
        $this->build_post_field();
        $this->set_opt();
        $this->result = curl_exec($this->curl);
        curl_close($this->curl);
        //$this->result = $decode ? json_decode($this->result, $type) : $this->result;
        return $this;
    }

    public function header($headers = [], $value = '')
    { //put add
        if (is_array($headers)) {
            foreach ($headers as $key => $value) {
                $this->header[$key] = $value;
            }
        } else {
            $this->header[$headers] = $value;
        }
        return $this;
    }

    protected function build_header()
    {
        foreach ($this->header as $key => $value) {
            $this->query_header[] = $key . ': ' . $value;
        }
        $this->opt[CURLOPT_HTTPHEADER] = $this->query_header; //set header to opt
    }

    public function opt($opt = [], $value = '') //put add

    {
        if (is_array($opt)) {
            foreach ($opt as $key => $value) {
                $this->opt[$key] = $value;
            }
        } else {
            $this->opt[$opt] = $value;
        }
        return $this;
    }

    protected function set_opt()
    {
        $this->check_method();
        $this->opt[CURLOPT_CUSTOMREQUEST] = $this->method;
        $this->opt[CURLOPT_URL] = $this->query_url;
        $this->opt[CURLOPT_POSTFIELDS] = $this->query_post_field;
        curl_setopt_array($this->curl, $this->opt);
    }

    public function token($token, $type = null)
    {
        $this->token_type = $type ? $type : $this->token_type;
        $this->token = $token;
        return $this;
    }

    protected function build_token()
    {
        return $this->header('Authorization', $this->token_type . ' ' . $this->token);
    }

    public function end_point(...$end_point)
    {
        $pre = func_get_args();
        if (!empty($pre)) {
            $to = is_array($pre[0]) ? $pre[0] : array_filter(explode('/', $pre[0])); // array_filter => filter not valid value, like null, 0, false
            foreach ($to as $value) {
                $this->end_point[] = $value;
            }
        }
        return $this;
    }

    protected function build_end_point()
    {
        $this->query_end_point = implode('/', $this->end_point);
    }

    protected function build_url()
    {
        $this->query_url = $this->base_url;
        if (!empty($this->query_end_point)) {
            $this->query_url .= '/' . $this->query_end_point;
        }
        if (!empty($this->query_url_para)) {
            $this->query_url .= '?' . $this->query_url_para;
        }
    }

    public function url_para($para = [], $value = null)
    {
        if (is_array($para)) {
            $this->url_para = $para;
        } else {
            $this->url_para[$para] = $value;
        }
        return $this;
    }

    protected function build_url_para()
    {
        $this->query_url_para = $this->build_http_query_para($this->url_para); //same with implement php=>http_build_query() function
    }

    public function post_field($para = [], $value = null)
    {
        if (is_array($para)) {
            $this->post_field = $para;
        } else {
            $this->post_field[$para] = $value;
        }
        return $this;
    }

    public function encode($flag = JSON_UNESCAPED_UNICODE, $depth = 512)
    {
        $this->encode_flag = $flag;
        $this->encode_depth = $depth;
        $this->chaeck_encode();
        return $this;
    }

    protected function build_post_field()
    {
        switch ($this->para_type) {
            case 'http':
                $this->query_post_field = $this->build_http_query_para($this->post_field); //same with implement php=>http_build_query() function
                break;
            case 'json':
                $this->query_post_field = json_encode($this->post_field, $this->encode_flag, $this->encode_depth);
                break;
        }
    }

    protected function build_http_query_para($para)
    { //because http para array level 1 without [], so need run one time here
        $this->check_http_query_para($para);
        $result = [];
        foreach ($para as $key => $value) {
            if (!is_array($value)) {
                $result[] .= "$key=$value";
            } else {
                foreach ($this->build_query_para_word($value, $key) as $word) {
                    $result[] .= $word;
                }
            }
        }
        return implode('&', $result);
    }

    protected function build_query_para_word(array $para = [], $pre_word = '')
    {
        $result = [];
        foreach ($para as $key => $value) {
            if (!is_array($value)) {
                $result[] = $pre_word . "[$key]=$value";
            } else {
                foreach ($this->build_url_para_word($value, $pre_word . "[$key]") as $word) {
                    $result[] .= $word;
                }
            }
        }
        return $result;
    }

    public function para_type($type = 'http')
    {
        $this->para_type = $type;
        $this->check_para_type();
        return $this;
    }

    public function test(...$test)
    {
        $this->build_header();
        return $this;
        dd(func_get_args());
    }
}
