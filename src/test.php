<?php


$result = parse_url('sftp://user:password@my_server.com/path/to/file.txt?test=test');

var_dump($result);
