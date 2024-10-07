<?php

require './vendor/autoload.php';

use WeiLun\WLCURL\Builder;
use WeiLun\WLCURL\Http\HttpBuilder;

// $builder = new Builder;
// $builder->url('http://localhost:234/path');
// var_dump($builder->buildOpts());

// $http_builder = new HttpBuilder;
// $http_builder->url('http://localhost:234/path%20t/http?test[www]=1&test[uuu]=2&qqq=234');
// var_dump($http_builder, $http_builder->buildUrl());

// http_build_url()

var_dump(rawurlencode('got space'));
var_dump(rawurldecode('got%20space'));

// var_dump(rawurldecode("test%20jlijasdfl-asd%2A-%25-.-%2F"));
