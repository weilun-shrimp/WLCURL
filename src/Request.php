<?php

namespace Weilun\WLCURL;

use CurlHandle;

class Request
{
    protected CurlHandle | false $row_curl;
    public CURL $curl;
}
