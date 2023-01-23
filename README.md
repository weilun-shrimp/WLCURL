# WLCURL

Let PHP Api (CURL) request more easyly、clearly、liberty and modelly<br>
讓 PHP Api (CURL) 請求更加簡單、清楚易懂、自由、模組化

*   [WLCURL](#wlcurl)
    *   [Installation - 安裝](#installation---安裝)
    *   [Quick Example - 快速範例](#quick-example---快速範例)
    *   [Construct - 構成式](#construct---構成式)
        *   [method](#method)
        *   [base_url](#base_url)
        *   [end_point](#end_point)
        *   [url_para](#url_para)
        *   [body](#body)
        *   [header](#header)
        *   [token](#token)
            *   [token_type](#token_type)
        *   [para_type](#para_type)
        *   [opt](#opt)
    *   [Execute - 執行](#execute---執行)
        *   [exe](#exe)
    *   [Error Handle - 錯誤處理](#error-handle---錯誤處理)
        *   [is_error](#is_error)
        *   [is_clien_error](#is_clien_error)
        *   [is_bad_request](#is_bad_request)
        *   [is_unauthorized](#is_unauthorized)
        *   [is_forbidden](#is_forbidden)
        *   [is_method_not_allow](#is_method_not_allow)
        *   [is_server_error](#is_server_error)
    *   [Get Request Result - 取得請求結果](#get-request-result---取得請求結果)
        *   [getBody](#getbody)
        *   [getdecodeBody](#getdecodebody)
    *   [Modelly-Best Advance Practice - 模組化-最佳進階做法](#modelly-best-advance-practice---模組化-最佳進階做法)
        *   [File Structure - 檔案結構](#file-structure---檔案結構)
        *   [MyApiServerApi](#myapiserverapi)
        *   [TargetApi](#targetapi)

## Installation - 安裝

```bash
$ composer require weilun/wlcurl
```

## Quick Example - 快速範例

> ```php
> use WeiLun/WLCURL;
>
> $order_api = new WLCURL; // Default GET method
> $order_api->base_url('https://my_api_server_url');
> $order_api->end_point('/order');
> $order_api->url_para(['page' => 1, 'page_size' => 24]);
> $order_api->exe();
> ```
Same as - 如下同上
> ```php
> use WeiLun/WLCURL;
>
> $order_api = (new WLCURL) // Default GET method
>   ->base_url('https://my_api_server_url');
>   ->end_point('/order');
>   ->url_para(['page' => 1, 'page_size' => 24]);
>   ->exe();
> ```
Same as - 如下同上
> ```php
> use WeiLun/WLCURL;
>
> $order_api = WLCURL::get([
>    'base_url' => 'https://my_api_server_url',
>    'end_point' => '/order',
>    'url_para' => ['page' => 1, 'page_size' => 24]
> ])->exe();
> ```
Same as above but modelly - 如下同上但模組化
> ```php
> use WeiLun/WLCURL;
>
> class MyApiServerApi extends WLCURL {
>    function __construct($para) {
>       $this->base_url = 'https://my_api_server_url';
>       parent::__construct($para);
>    }
> }
>
> class OrderApi extends MyApiServerApi {
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

### base_url
>
> As we all knows, URL is the most important foundation of the CURL request. And <font color=red>_basic url_</font> is the first section of the url<br>
> 眾所周知, 網址是 curl 請求最重要的一環, 而 <font color=red>_basic url_</font> 是組成網址的第一個部分
>
> ```php
> $api = WLCURL::get()->base_url('https://my_api_server_url');
> ```

### end_point
>
> <font color=red>_end\_point_</font> is the second section of the url, reach your target node<br>
> <font color=red>_end\_point_</font> 是組成網址的第二個部分, 觸及你的目標節點
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
> <font color=red>_url\_para_</font> is the third section of the url, pass your requirement parameter to api server<br>
> <font color=red>_url\_para_</font> 是組成網址的第三個部分, 傳送你所需的參數給目標 api 伺服器
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
>> It will put <font color=red>_token\_type_</font> value in front of token as soon as build token. Defualt value is <font color=blue>_"Bearer"_</font><br>
>> 在組建token參數時, 會將<font color=red>_token\_type_</font>值擺在前面
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
> Set PHP original curl opt parameter, you can find referance in [PHP CURL setopt](https://www.php.net/manual/en/function.curl-setopt.php)<br>
> 設置PHP原生curl opt參數, 你可以參照此處[PHP CURL setopt](https://www.php.net/manual/en/function.curl-setopt.php)
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
> <font color=red>_WLCURL_</font> will do request task as soon as you call <font color=red>_exe()_</font> function, and <font color=red>_WLCURL_</font> will not do anything before you call it.<br>
> <font color=red>_WLCURL_</font> 會在執行 <font color=red>_exe()_</font> 函式時執行請求任務. <font color=red>_WLCURL_</font> 不會在你呼叫此函式前做任何事.
> ```php
> $api = (new WLCURL) // Default GET method
>    ->base_url('https://my_api_server_url');
>    ->end_point('/order');
>    ->url_para(['page' => 1, 'page_size' => 24]);
>    ->exe();
> ```


## Error Handle - 錯誤處理
<font color=red>_WLCURL_</font> is already prepare multiple function to help you handle your error. It only has meaning after <font color=red>_exe()_</font><br>

<font color=red>_WLCURL_</font> 已經準備好許多函式來幫助你處理錯誤狀況. 這只會在執行 <font color=red>_exe()_</font> 後有意義

### is_error
> check curl request result return first section http status code is in <font color=blue>_4_</font> or <font color=blue>_5_</font><br>
> Return type <font color=red>_boolean_</font><br>
> 檢查curl請求結果回傳的http狀態碼第一字節是否是 <font color=blue>_4_</font> 或 <font color=blue>_5_</font><br>
> 回傳型態 <font color=red>_boolean_</font>
> ```php
> $api->is_error();
> ```

### is_clien_error
> check curl request result return first section http status code is <font color=blue>_4_</font> or not<br>
> Return type <font color=red>_boolean_</font><br>
> 檢查curl請求結果回傳的http狀態碼第一字節是否是 <font color=blue>_4_</font><br>
> 回傳型態 <font color=red>_boolean_</font>
> ```php
> $api->is_clien_error();
> ```

### is_bad_request
> check curl request result return http status code is <font color=blue>_400_</font> or not<br>
> Return type <font color=red>_boolean_</font><br>
> 檢查curl請求結果回傳的http狀態碼是否是 <font color=blue>_400_</font><br>
> 回傳型態 <font color=red>_boolean_</font>
> ```php
> $api->is_bad_request();
> ```

### is_unauthorized
> check curl request result return http status code is <font color=blue>_401_</font> or not<br>
> Return type <font color=red>_boolean_</font><br>
> 檢查curl請求結果回傳的http狀態碼是否是 <font color=blue>_401_</font><br>
> 回傳型態 <font color=red>_boolean_</font>
> ```php
> $api->is_unauthorized();
> ```

### is_forbidden
> check curl request result return http status code is <font color=blue>_403_</font> or not<br>
> Return type <font color=red>_boolean_</font><br>
> 檢查curl請求結果回傳的http狀態碼是否是 <font color=blue>_403_</font><br>
> 回傳型態 <font color=red>_boolean_</font>
> ```php
> $api->is_forbidden();
> ```

### is_method_not_allow
> check curl request result return http status code is <font color=blue>_405_</font> or not<br>
> Return type <font color=red>_boolean_</font><br>
> 檢查curl請求結果回傳的http狀態碼是否是 <font color=blue>_405_</font><br>
> 回傳型態 <font color=red>_boolean_</font>
> ```php
> $api->is_method_not_allow();
> ```

### is_server_error
> check curl request result return first section http status code is <font color=blue>_5_</font> or not<br>
> Return type <font color=red>_boolean_</font><br>
> 檢查curl請求結果回傳的http狀態碼第一字節是否是 <font color=blue>_5_</font><br>
> 回傳型態 <font color=red>_boolean_</font>
> ```php
> $api->is_server_error();
> ```

### get_Http_code
> Retrieve raw http status code
> 取得原生http回傳之狀態碼
> ```php
> $api->get_Http_code();
> if ($api->get_Http_code() != 200) echo 'Do something.';
> ```

### get_error_msg
> Retrieve the error msg from php original curl
> 取得PHP原生curl請求錯誤的錯誤訊息
> ```php
> $api->get_error_msg();
> ```

### get_info
> Retrieve full request info from PHP original curl
> 取得所有PHP原生curl請求的回傳訊息
> ```php
> $api->get_info();
> ```


## Get Request Result - 取得請求結果
It only has meaning after <font color=red>_exe()_</font>.<br>
只有在呼叫<font color=red>_exe()_</font>函式後有意義.

### getBody
> Get raw request result body.<br>
> 取得原始請求後的body結果.
> ```php
> $result = $api->getBody();
> ```

### getdecodeBody
> Get request result body that after [json_decode](https://www.php.net/manual/en/function.json-decode.php).<br>
> 取得請求後的 [json_decode](https://www.php.net/manual/en/function.json-decode.php) body結果.
> ```php
> $result = $api->getdecodeBody();
> //Same as
> $result = json_decode($api->getBody());
> //There have three option parameter to config decode result same as php json_decode()
> $result = $api->getdecodeBody($associative = null, int $depth = 512, int $flags = 0);
> ```

You may want to handle error before retrieve body<br>
你也許會想要在取得結果前做錯誤處理
```php
if ($api->is_error()) throw new \Exception('Somethong go wrong.');
$result = $api->getBody();
```


## Modelly-Best Advance Practice - 模組化-最佳進階做法
Make your own packaged curl model. <br>
製作屬於你自己的curl請求模組類別
### File Structure - 檔案結構
`Models`<br>
|- MyApiServerApi (extends <font color=red>_WLCURL_</font>)<br>
|- `Order`<br>
|-- OrderApi (extends <font color=red>_MyApiServerApi_</font>)<br>
|-- OrderProductApi (extends <font color=red>_MyApiServerApi_</font>)<br>

### MyApiServerApi
> Make your own target api server model class.<br>
> You can set any solid required curl parameter in this model constructor. And you don't have to do this again.<br>
> 製作你自己的目標api請求模組類別.<br>
> 你可以設置任何請求前所需的目標參數在構成式裡, 你將不需要再做一次.<br>
> ```php
> namespace App\Models;
>
> use WeiLun\WLCURL;
>
> class MyApiServerApi extends WLCURL {
>     function __construct($para) {
>         $this->base_url('https://my_api_server_url');
>         $this->token('My Api Server Token.');
>         $this->para_type('json');
>         parent::__construct($para);
>    }
> }
> ```

### TargetApi
> Make your own target end point model class. And package your api function.<br>
> 製作你自己的目標節點請求api模組類別. 並打包函式方便日後使用.
> ```php
> namespace App\Models\Order;
>
> use App\Models\MyApiServerApi;
>
> class OrderApi extends MyApiServerApi {
>     function __construct($para) {
>         $this->end_point('/order');
>         parent::__construct($para);
>     }
>
>     public static function index(int $page = 1, int $pae_size = 24) {
>         return self::get([
>             'url_para' => [
>                 'page' => $page,
>                 'page_size' => $page_size
>             ]
>         ])->exe();
>     }
>
>     public static function retrieve(int $id) {
>         $self = self::get();
>         $self->end_point("/$id", true); // Make end_point become /order/{id}
>         return $self->exe();
>     }
>
>     public static function create(array $body) {
>         return self::post([
>             'body' => $body
>         ])->exe();
>     }
>
>     public static function update(int $id, array $body) {
>         $self = self::put();
>         $self->end_point("/$id", true); // Make end_point become /order/{id}
>         $self->body($body);
>         return $self->exe();
>     }
> }
> ```
> You can use TargetApi (OrderApi) like<br>
> 你可以使用 TargetApi (OrderApi) 就像
> ```php
> use App\Models\Order\OrderApi;
>
> $order_index_api = OrderApi::index(1, 24);
> $order_api = OrderApi::retrieve(1);
> $order_api = OrderApi::create([
>     'customer_name' => 'My customer name',
>     'total' => 100
> ]);
> $order_update_api = OrderApi::update(1, [
>     'customer_name' => 'Changed customer name',
>     'total' => 10
> ])
> ```