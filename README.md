# WLCURL

Let PHP Api (CURL) request more easyly、clearly and modelly<br>
讓 PHP Api (CURL) 請求更加簡單、清楚易懂、模組化

-   [WLCURL](#wlcurl)
    -   [Installation](#installation)
    -   [Quick Example](#quick-example)
    -   [Basic Usage](#basic-usage)
        -   [basic_url](#basic_url)

## Installation - 安裝

```bash
$ composer require WLCURL/WLCURL
```

## Quick Example - 快速範例

> ```php
> use WLCURL/WLCURL;
>
> $api = new WLCURL; // Default GET method
> $api->basic_url('https://my_api_server_url');
> $api->end_point('/my_end_point');
> $api->url_para(['page' => 1, 'page_size' => 24]);
> $api->exe();
> ```

## Basic Usage - 基礎用法

> ### basic_url
>
> As we all knows, URL is the most important foundation of the CURL request. And <font color=red>_basic url_</font> is first section of the url<br>
> 眾所周知, 網址是 curl 請求最重要的一環, 而 <font color=red>_basic url_</font> 是組成網址的第一個部分
>
> ```php
> use WLCURL/WLCURL;
>
> $api = new WLCURL;
> $api->basic_url('https://my_api_server_url');
> ```
