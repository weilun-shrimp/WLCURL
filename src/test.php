<?php


$result = parse_url('h://my_server.com#eee');

var_dump($result);

parse_str('test=test', $query);

var_dump($query);
