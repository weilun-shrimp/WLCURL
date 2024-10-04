<?php

namespace Weilun\WLCURL\Tests;

use PHPUnit\Framework\TestCase;
use WeiLun\WLCURL\Curl;

class CurlTest extends TestCase
{
    /**
     * scheme, user, pass, host, fragment, port
     */
    public function testStraightAttributes()
    {
        $curl = new Curl;
        // string
        foreach (['scheme', 'user', 'pass', 'host', 'fragment'] as $v) {
            // default value
            $this->assertSame('', $curl->{$v});
            // straight assignment value
            $curl->{$v} = "straight_assignment_$v";
            $this->assertSame("straight_assignment_$v", $curl->{$v});
            // function assignment value
            $return = $curl->{$v}("funciton_assignment_$v");
            $this->assertSame("funciton_assignment_$v", $curl->{$v});
            $this->assertSame($curl, $return);
        }
        // int (port)
        // default value
        $this->assertSame(0, $curl->port);
        // straight assignment value
        $curl->port = 443;
        $this->assertSame(443, $curl->port);
        // function assignment value
        $return = $curl->port(8000);
        $this->assertSame(8000, $curl->port);
        $this->assertSame($curl, $return);
    }

    public function testPath()
    {
        $curl = new Curl;
        // default value
        $this->assertSame('', $curl->path);
        // straight assignment value
        $curl->path = 'straight_assignment_path';
        $this->assertSame('straight_assignment_path', $curl->path);
        // function assignment value
        $return = $curl->path('funciton_assignment_path');
        $this->assertSame('funciton_assignment_path', $curl->path);
        $this->assertSame($curl, $return);
        // function mode
        // replace mode
        $return = $curl->path('/replace_path', 'replace');
        $this->assertSame('/replace_path', $curl->path);
        $this->assertSame($curl, $return);
        // add mode
        $return = $curl->path('/add_path', 'add');
        $this->assertSame('/replace_path/add_path', $curl->path);
        $this->assertSame($curl, $return);
        // exception mode
        $this->expectException(\Exception::class);
        $curl->path('/add_path', 'invalid_mode_string');
    }

    public function testQuery()
    {
        $curl = new Curl;
        // default value
        $this->assertSame([], $curl->queries);
        // straight assignment value
        $curl->queries = ['test' => 'test'];
        $this->assertSame(['test' => 'test'], $curl->queries);
        // function assignment value
        $return = $curl->queries(['test_function_assignment' => 'test_function_assignment']);
        $this->assertSame(['test_function_assignment' => 'test_function_assignment'], $curl->queries);
        $this->assertSame($curl, $return);
        // function mode
        // replace mode
        $return = $curl->queries(['test_replace_mode' => 'test_replace_mode'], 'replace');
        $this->assertSame(['test_replace_mode' => 'test_replace_mode'], $curl->queries);
        $this->assertSame($curl, $return);
        // add mode
        $return = $curl->queries(['test_add_mode' => 'test_add_mode'], 'add');
        $this->assertSame([
            'test_replace_mode' => 'test_replace_mode',
            'test_add_mode' => 'test_add_mode'
        ], $curl->queries);
        $this->assertSame($curl, $return);
        // exception mode
        $this->expectException(\Exception::class);
        $curl->queries(['test_add_mode' => 'test_add_mode'], 'invalid_mode_string');

        // query function
        $return = $curl->query('test_query_func', 'test_query_func');
        $this->assertSame([
            'test_replace_mode' => 'test_replace_mode',
            'test_add_mode' => 'test_add_mode',
            'test_query_func' => 'test_query_func'
        ], $curl->queries);
        $this->assertSame($curl, $return);
    }

    public function testOpt()
    {
        $curl = new Curl;
        // default value
        $this->assertSame([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ], $curl->opts);
        // straight assignment value
        $curl->opts = [CURLOPT_HTTPGET => true];
        $this->assertSame([CURLOPT_HTTPGET => true], $curl->opts);
        // function assignment value
        $return = $curl->opts([CURLOPT_RETURNTRANSFER => true]);
        $this->assertSame([CURLOPT_RETURNTRANSFER => true], $curl->opts);
        $this->assertSame($curl, $return);
        // function mode
        // replace mode
        $return = $curl->opts([CURLOPT_TIMEOUT => 10], 'replace');
        $this->assertSame([CURLOPT_TIMEOUT => 10], $curl->opts);
        $this->assertSame($curl, $return);
        // add mode
        $return = $curl->opts([CURLOPT_RETURNTRANSFER => true], 'add');
        $this->assertArrayHasKey(CURLOPT_RETURNTRANSFER, $curl->opts);
        $this->assertArrayHasKey(CURLOPT_TIMEOUT, $curl->opts);
        $this->assertSame(true, $curl->opts[CURLOPT_RETURNTRANSFER]);
        $this->assertSame(10, $curl->opts[CURLOPT_TIMEOUT]);
        $this->assertSame($curl, $return);
        // exception mode
        $this->expectException(\Exception::class);
        $curl->opts([CURLOPT_HTTPGET => true], 'invalid_mode_string');
        // key not int
        $this->expectException(\Exception::class);
        $curl->opts(['key_not_int' => true]);

        // opt function
        $return = $curl->opt(CURLOPT_HTTPGET, true);
        $this->assertArrayHasKey(CURLOPT_RETURNTRANSFER, $curl->opts);
        $this->assertArrayHasKey(CURLOPT_TIMEOUT, $curl->opts);
        $this->assertArrayHasKey(CURLOPT_HTTPGET, $curl->opts);
        $this->assertSame(true, $curl->opts[CURLOPT_RETURNTRANSFER]);
        $this->assertSame(10, $curl->opts[CURLOPT_TIMEOUT]);
        $this->assertSame(true, $curl->opts[CURLOPT_HTTPGET]);
        $this->assertSame($curl, $return);
    }

    public function testUrl()
    {
        // http
        $curl = new Curl;
        $return = $curl->url('http://www.example.com:8000/path?query=string#fragment');
        $this->assertSame($curl, $return);
        $this->assertSame('http', $curl->scheme);
        $this->assertSame('www.example.com', $curl->host);
        $this->assertSame(8000, $curl->port);
        $this->assertSame('/path', $curl->path);
        $this->assertSame('fragment', $curl->fragment);
        $this->assertSame(['query' => 'string'], $curl->queries);

        // ftp or else
        $curl = new Curl;
        $return = $curl->url('ftp://username:password@www.example.com/path');
        $this->assertSame($curl, $return);
        $this->assertSame('ftp', $curl->scheme);
        $this->assertSame('www.example.com', $curl->host);
        $this->assertSame('/path', $curl->path);
        $this->assertSame('username', $curl->user);
        $this->assertSame('password', $curl->pass);
    }
}
