# WLCURL

Let PHP Api (CURL) request more easyly、clearly、liberty and modelly<br>
讓 PHP Api (CURL) 請求更加簡單、清楚易懂、自由、模組化

-   [WLCURL](#wlcurl)
    -   [Installation](#installation---安裝)
    -   [Quick Example](#quick-example---快速範例)
    -   [Construct](#construct---構成式)
        -   [method](#method)
        -   [basic_url](#basic_url)
        -   [end_point](#end_point)
        -   [url_para](#url_para)
        -   [body](#body)
        -   [header](#header)
        -   [token](#token)
            -   [token_type](#token_type)
        -   [para_type](#para_type)
        -   [opt](#opt)
    -   [Execute](#execute---執行)
        -   [exe](#exe)
    -   [Error Handle](#error-handle---錯誤處理)

## Installation - 安裝

```bash
$ composer require WLCURL/WLCURL
```

## Quick Example - 快速範例

> ```php
> use WLCURL/WLCURL;
>
> $order_api = new WLCURL; // Default GET method
> $order_api->basic_url('https://my_api_server_url');
> $order_api->end_point('/order');
> $order_api->url_para(['page' => 1, 'page_size' => 24]);
> $order_api->exe();
> ```
Same as - 如下同上
> ```php
> use WLCURL/WLCURL;
>
> $order_api = (new WLCURL) // Default GET method
>   ->basic_url('https://my_api_server_url');
>   ->end_point('/order');
>   ->url_para(['page' => 1, 'page_size' => 24]);
>   ->exe();
> ```
Same as - 如下同上
> ```php
> use WLCURL/WLCURL;
>
> $order_api = WLCURL::get([
>    'basic_url' => 'https://my_api_server_url',
>    'end_point' => '/order',
>    'url_para' => ['page' => 1, 'page_size' => 24]
> ])->exe();
> ```
Same as down below but modelly - 如下同上但模組化
> ```php
> use WLCURL/WLCURL;
>
> class MyApiServer extends WLCURL {
>    function __construct($para) {
>       $this->basic_url = 'https://my_api_server_url';
>       parent::__construct($para);
>    }
> }
>
> class OrderApi extends MyApiServer {
>    function __construct($para) {
>       $this->end_point = '/order';
>       parent::__construct($para);
>    }
>
>    public static function fetch_index(array $url_para = []) {
>       return (new self([
>          'url_para' => $url_para
>       ]))->exe();
>    }
> }
>
> $api = OrderApi::fetch_index([
>    'url_para' => ['page' => 1, 'page_size' => 24]
> ]);
> ```
Reach result and error handle
> ```php
> if ($api->is_error()) throw new \Exception('Somethong go wrong.');
> $result = $api->getBody(); // fetch result
> ```


## Construct - 構成式

### method
>
> There are already have multiple default function to help you to set curl <font color=red>_method_</font><br>
> 已經有許多默認函式提供讓你去設置請求curl的<font color=red>_method_</font>
>
> ```php
> $api = new WLCURL(array $my_construct_para = []); // Default GET method
> $api = WLCURL::get(array $my_construct_para = []);
> $api = WLCURL::post(array $my_construct_para = []);
> $api = WLCURL::put(array $my_construct_para = []);
> $api = WLCURL::patch(array $my_construct_para = []);
> $api = WLCURL::delete(array $my_construct_para = []);
> ```
>> Customize method - 客製方法
>> ```php
>> $api = new WLCURL;
>> $api->method = 'My custom method';
>> // Same as above
>> $api = new WLCURL(['method' => 'My custom method']); 
>> $api = WLCURL::request('My custom method', array $my_construct_para = []); 
>> 
>> ```

### basic_url
>
> As we all knows, URL is the most important foundation of the CURL request. And <font color=red>_basic url_</font> is the first section of the url<br>
> 眾所周知, 網址是 curl 請求最重要的一環, 而 <font color=red>_basic url_</font> 是組成網址的第一個部分
>
> ```php
> $api = WLCURL::get()->basic_url('https://my_api_server_url');
> ```

### end_point
>
> <font color=red>_end_point_</font> is the second section of the url, reach your target node<br>
> <font color=red>_end_point_</font> 是組成網址的第二個部分, 觸及你的目標節點
>
> ```php
> $api = WLCURL::get()->end_point('/order');
> ```
> If you want to add end point node, put <font color=blue>_true_</font> in second parameter
> 如果你想要壘加目標節點, 在第二個參數放上 <font color=blue>_true_</font>
> ```php
> $api->end_point('/{id}', true);
> // Same as
> $api = WLCURL::get()->end_point('/order/{id}');
> ```

### url_para
>
> <font color=red>_url_para_</font> is the third section of the url, pass your requirement parameter to api server<br>
> <font color=red>_url_para_</font> 是組成網址的第三個部分, 傳送你所需的參數給目標 api 伺服器
>
> ```php
> $api = WLCURL::get()
>   ->url_para('page', 1);
>   ->url_para('page_size', 24);
> // Same as
> $api = WLCURL::get()->url_para([
>    'page' => 1, 
>    'page_size' => 24
> ]);
> ```
> It will generate like <font color=red>_'?page=1&page_size=24'_</font> string<br>
> 這會生成像是 <font color=red>_'?page=1&page_size=24'_</font> 的字串

### body
>
> Pass your requirement post field parameter to api server<br>
> 傳送你所需的post field參數給目標 api 伺服器
>
> ```php
> $api = WLCURL::post()
>   ->body('title', 'My title'); // Add parameter in body structure
>   ->body('content', 'My content');
> // Same as
> $api = WLCURL::post()->body([ // Replace whole body structure
>    'title' => 'My title', 
>    'content' => 'My content'
> ]);
> ```

### header
>
> ```php
> $api = WLCURL::get()->header('Cache-Control', 'no-cache'); // Add parameter
> // Same as
> $api = WLCURL::get()->header(['Cache-Control' => 'no-cache']); // Add parameter
> // Same as
> $api = WLCURL::get()->opt(CURLOPT_HTTPHEADER, ["Cache-Control: no-cache"]); // Replace whole curl header structure
> ```
> It will build header structure like <font color=red>[_"Cache-Control: no-cache"_]</font>

### token
>
> ```php
> $api = WLCURL::get()->token('My token');
> ```
>### token_type
>>
>> It will put <font color=red>_token_type_</font> value in front of token as soon as build token. Defualt value is <font color=blue>_"Bearer"_</font><br>
>> 在組建token參數時, 會將<font color=red>_token_type_</font>值擺在前面
>> 
>> ```php
>> $api = WLCURL::get()->token_type('Bearer');
>> ```
> <br> If you want to set token manually 
> <br> 如果你想手動設置token 
> ```php
> $api = WLCURL::get()->header('Authorization', 'My token type' . 'My token');
> ```

### para_type
> It will effect the <font color=red>_body_</font> parameter formation as soon as build curl request.<br> 
> The default value is <font color=blue>"_http_"</font>.<br> 
> The value only accept in <font color=blue>_["http", "json"]_</font>.<br>
> If value equal <font color=blue>_"http"_</font>, <font color=red>_WLCURL_</font> will format <font color=red>_body_</font> as [build_http_query_para](https://www.php.net/manual/en/function.http-build-query.php). <br>
> If value equal <font color=blue>_"json"_</font>, <font color=red>_WLCURL_</font> will format <font color=red>_body_</font> as [json_encode](https://www.php.net/manual/en/function.json-encode.php), and set curl header Content-Type as <font color=blue>_"application/json"_</font> automatically. <br>
> 此參數會直接影響curl請求時<font color=red>_body_</font>參數轉換的形式<br>
> 預設值是<font color=blue>"_http_"</font>.<br> 
> 參數容許值只在<font color=blue>_["http", "json"]_</font>裡面.<br>
> 如果參數設為<font color=blue>_"http"_</font>, <font color=red>_WLCURL_</font> 會將 <font color=red>_body_</font>參數設為 [build_http_query_para](https://www.php.net/manual/en/function.http-build-query.php). <br>
> 如果參數設為<font color=blue>_"json"_</font>, <font color=red>_WLCURL_</font> 會將 <font color=red>_body_</font>參數設為 [json_encode](https://www.php.net/manual/en/function.json-encode.php), 並且會自動將curl header Content-Type參數設為 <font color=blue>_"application/json"_</font><br>
> 
> ```php
> $api = WLCURL::get()->para_type('json');
>    //->header('Content-Type', 'application/json'); If value is "json", WLCURL will set this automatically
> ```

### opt
>
> Set curl opt parameter, you can find referance in [PHP CURL setopt](https://www.php.net/manual/en/function.curl-setopt.php)
> ```php
> $api = WLCURL::get()->opt(CURLOPT_RETURNTRANSFER, true); // Add parameter
> // Same as
> $api = WLCURL::get()->header([CURLOPT_RETURNTRANSFER => true]); // Add parameter
> // Same as
> $api = WLCURL::get();
> $api->opt = [CURLOPT_HTTPHEADER => true]; // Replace whole curl opt structure, It's dangerous, please becareful.
> ```


## Execute - 執行
### exe
> <font color=red>_WLCURL_</font> will do request task as soon as you call <font color=red>_exe()_</font> function, and <font color=red>_WLCURL_</font> will not do anything before you call it.
> ```php
> $api = (new WLCURL) // Default GET method
>    ->basic_url('https://my_api_server_url');
>    ->end_point('/order');
>    ->url_para(['page' => 1, 'page_size' => 24]);
>    ->exe();
> ```


## Error Handle - 錯誤處理
### exe
> <font color=red>_WLCURL_</font> will do request task as soon as you call <font color=red>_exe()_</font> function, and <font color=red>_WLCURL_</font> will not do anything before you call it.
> ```php
> $api = (new WLCURL) // Default GET method
>    ->basic_url('https://my_api_server_url');
>    ->end_point('/order');
>    ->url_para(['page' => 1, 'page_size' => 24]);
>    ->exe();
> ```
