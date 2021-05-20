<?php

namespace App\Models\WLCURL;

class WLCURL
{
    public $curl;

    public $base_url;
    public $end_point = [];
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

    //result
    protected $Body;
    protected $info;
    protected $error;

    //for check obj para
    protected $check_method = ['GET', 'POST', 'PUT', 'DELETE'];
    protected $check_para_type = ['http', 'json'];
    protected $check_multiple_para = [
        'base_url',
        'header',
        'opt',
        'end_point',
        'token',
        'url_para',
        'post_field',
        'para_type',
    ];

    public function __construct($base_url = null)
    {
        $this->base_url = $base_url ?? $this->base_url;
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
                throw new \Exception('WLCURL method error,only accept [' . implode(', ', $this->check_method) . '], please check and try again.');
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
                throw new \Exception('WLCURL query type error,only accept [' . implode(', ', $this->check_para_type) . '], please check and try again.');
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
                throw new \Exception('WLCURL query para error, url or post field, type must be array, please check and try again.');
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
                throw new \Exception('WLCURL encode depth error, type must be numeric, please check and try again.');
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    public function exe()
    {
        //check first
        $this->check_method();
        $this->check_para_type();
        $this->check_http_query_para($this->url_para);
        $this->check_http_query_para($this->post_field);

        $this->curl = curl_init(); // init curl
        $this->build_end_point();
        $this->build_url_para();
        $this->build_url();
        $this->build_token(); // must build before build header, because token need set in header
        $this->build_header(); // prepare header and set header and prepare to opt
        $this->build_post_field();
        $this->set_opt();
        $this->Body = curl_exec($this->curl);
        $this->info = curl_getinfo($this->curl);
        $this->error = curl_error($this->curl);
        curl_close($this->curl);
        //$this->result = $decode ? json_decode($this->result, $type) : $this->result;
        return $this;
    }

    public function base_url(string $base_url = '')
    {
        $this->base_url = $base_url;
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

    /**
     * referance : 
     *      https://www.php.net/manual/en/function.curl-setopt.php
     *      https://www.php.net/manual/en/function.curl-setopt-array.php
     */
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
        $this->opt[CURLOPT_CUSTOMREQUEST] = strtoupper($this->method);
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

    protected function check_end_point()
    {
        try {
            if (!is_string($this->end_point)) {
                throw new \Exception('WLCURL end point error, must be string, please check and try again.');
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    public function end_point(string $end_point = '', bool $is_add = false)
    {
        if($is_add) $this->end_point .= (string) $end_point;
        else $this->end_point = (string) $end_point;
        return $this;
    }

    protected function build_end_point()
    {
        $this->check_end_point();
        $this->query_end_point = $this->end_point;
    }

    /**
     * Because some frame work has uniqe rule to format api request url
     * example : Django -> if request method is post , every endpoint end need add "/"
     * if use this method will cause error, so I command this method 
     */
    // public function end_point(...$end_point)
    // {
    //     $pre = func_get_args();
    //     if (!empty($pre)) {
    //         $to = is_array($pre[0]) ? $pre[0] : array_filter(explode('/', $pre[0])); // array_filter => filter not valid value, like null, 0, false
    //         foreach ($to as $value) {
    //             $this->end_point[] = $value;
    //         }
    //     }
    //     return $this;
    // }

    // protected function build_end_point()
    // {
    //     $this->query_end_point = implode('/', $this->end_point);
    // }

    protected function build_url()
    {
        $this->query_url = $this->base_url;
        if (!empty($this->query_end_point)) {
            $this->query_url .= $this->query_end_point;
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

    /**
     * referance : https://www.php.net/manual/en/function.json-encode.php
     */
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
                foreach ($this->build_query_para_word($value, $pre_word . "[$key]") as $word) {
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
        switch ($type) {
            case 'http':
                break; // do nothing
            case 'json':
                $this->header('Content-Type', 'application/json');
                break;
        }
        return $this;
    }


    

    /**
     *  Accept para
     *  ---------------
     *      base_url
     *      header
     *      opt
     *      end_point
     *      token
     *      url_para
     *      post_field
     *      para_type
     */
    protected function check_multiple_para($multiple_para)
    {
        try {
            $check_keys = array_keys($multiple_para);
            foreach($check_keys as $value){
                if(!in_array($value, $this->check_multiple_para)){
                    throw new \Exception("WLCURL query para error, para [\"$value\"] not valid, key must be in [ " . implode(', ', $this->check_multiple_para) . " ], please check and try again.");
                }
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    protected function build_multiple_para($multiple_para)
    {
        $this->check_multiple_para($multiple_para);
        foreach($multiple_para as $key => $value){
            $this->{$key}($value);
        }
    }

    /**
     * request method -----------------
     */


    /**
     * example-------------
     *      $response = (new WLCURL)->request('POST', [
     *          'base_url' => 'https://your.destination.url',
     *          'end_point' => 'where/your/wanna/go',
     *          'url_para' => [
     *              'key' => 'value',
     *          ],
     *          'header' => [
     *              'Content-Type' => 'application/json',
     *              'Authorization' => 'Your token here',       
     *          ]
     *      ])->exe();
     */
    public static function request(string $method = 'GET', array $multiple_para = [])
    {
        $self = new static;
        if($method) $self->method = strtoupper($method);
        $self->build_multiple_para($multiple_para);
        return $self;
    }

    public static function get(array $multiple_para = [])
    {
        $self = new static;
        $self->build_multiple_para($multiple_para);
        return $self;
    }

    public static function post(array $multiple_para = [])
    {
        $self = new static;
        $self->method = strtoupper(__FUNCTION__);
        $self->build_multiple_para($multiple_para);
        return $self;
    }

    public static function put(array $multiple_para = [])
    {
        $self = new static;
        $self->method = strtoupper(__FUNCTION__);
        $self->build_multiple_para($multiple_para);
        return $self;
    }

    public static function delete(array $multiple_para = [])
    {
        $self = new static;
        $self->method = strtoupper(__FUNCTION__);
        $self->build_multiple_para($multiple_para);
        return $self;
    }


    /**
     * after exe 
     */
    public function getBody()
    {

    }

    /**
     * accept para type
     * -----------------
     *      $associative    bool|null
     *      $depth          int
     *      $flags          int
     */
    public function getdecodeBody($associative = null , int $depth = 512 , int $flags = 0 )
    {
        return json_decode($this->Body, $associative, $depth, $flags);
    }

    /**
     *   accept target para
     *   -------------------
     *      url
     *      content_type 
     *      http_code 
     *      header_size 
     *      request_size 
     *      filetime 
     *      ssl_verify_result 
     *      redirect_count 
     *      total_time 
     *      namelookup_time 
     *      connect_time 
     *      pretransfer_time 
     *      size_upload 
     *      size_download 
     *      speed_download 
     *      speed_upload 
     *      download_content_length 
     *      upload_content_length 
     *      starttransfer_time 
     *      redirect_time 
     *      redirect_url 
     *      primary_ip 
     *      certinfo 
     *      primary_port 
     *      local_ip 
     *      local_port 
     *      http_version 
     *      protocol 
     *      ssl_verifyresult 
     *      scheme 
     *      appconnect_time_us 
     *      connect_time_us 
     *      namelookup_time_us 
     *      pretransfer_time_us 
     *      redirect_time_us 
     *      starttransfer_time_us 
     *      total_time_us 
     */
    protected function get_info($target = null)
    {
        if($target) return $this->info[$target];
        else return $this->info;
    }

    /**
     *  Http status code
     */
    protected function check_http_code(string $function_name = '')
    {
        try {
            if (!isset($this->info['http_code'])) {
                throw new \Exception("Http code not declare, function $function_name must called after request(exe) CURL, please check and try again.");
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die;
        }
    }

    protected function get_Http_code()
    {
        $this->check_http_code(__FUNCTION__);
        return $this->info['http_code'];
    }

    /**
     *  Http status code check method
     */
    protected function is_error()
    {
        $this->check_http_code(__FUNCTION__);
        return (substr($this->info['http_code'], 0, 1) == 4 || substr($this->info['http_code'], 0, 1) == 5) ? true : false;
    }

    /**
     *  clien error
     */
    protected function is_clien_error()
    {
        $this->check_http_code(__FUNCTION__);
        return substr($this->info['http_code'], 0, 1) == 4 ? true : false;
    }

    protected function is_bad_request()
    {
        $this->check_http_code(__FUNCTION__);
        return $this->info['http_code'] == 400 ? true : false;
    }

    protected function is_unauthorized()
    {
        $this->check_http_code(__FUNCTION__);
        return $this->info['http_code'] == 401 ? true : false;
    }

    protected function is_forbidden()
    {
        $this->check_http_code(__FUNCTION__);
        return $this->info['http_code'] == 403 ? true : false;
    }

    protected function is_method_not_allow()
    {
        $this->check_http_code(__FUNCTION__);
        return $this->info['http_code'] == 405 ? true : false;
    }

    /**
     *  server error
     */
    protected function is_server_error()
    {
        $this->check_http_code(__FUNCTION__);
        return substr($this->info['http_code'], 0, 1) == 5 ? true : false;
    } 

    /**
     * referance : https://www.php.net/manual/en/function.curl-error.php
     */
    protected function get_error_msg()
    {
        return $this->error;
    }
}
