<?php

namespace Weilun\WLCURL\Tests;

use PHPUnit\Framework\TestCase;
use WeiLun\WLCURL\Builder;

class BuilderTest extends TestCase
{
    /**
     * scheme, user, pass, host, fragment, port
     */
    public function testStraightStringAttributes()
    {
        $builder = new Builder;
        foreach (['scheme', 'user', 'pass', 'host', 'fragment'] as $v) {
            // default value
            $this->assertSame('', $builder->{$v});
            // straight assignment value
            $builder->{$v} = "straight_assignment_$v";
            $this->assertSame("straight_assignment_$v", $builder->{$v});
            // function assignment value
            $return = $builder->{$v}("funciton_assignment_$v");
            $this->assertSame("funciton_assignment_$v", $builder->{$v});
            $this->assertSame($builder, $return);
        }
    }

    public function testScheme()
    {
        $builder = new Builder;
        // invalid scheme
        $this->expectException(\Exception::class);
        $builder->scheme = 'h ttp';
        $this->expectException(\Exception::class);
        $builder->scheme('htt p');
    }

    public function testRFC3986()
    {
        $builder = new Builder;
        foreach (['user', 'pass', 'host', 'fragment'] as $v) {
            // straight assignment value
            $builder->{$v} = 'got%20space';
            $this->assertSame('got space', $builder->{$v});
            // function assignment value
            $builder->{$v}('got%20space');
            $this->assertSame('got space', $builder->{$v});
        }
        // path
        $builder->path = '/got%20space/got%20space';
        $this->assertSame('/got space/got space', $builder->path);
        $builder->path('/got%20space/got%20space');
        $this->assertSame('/got space/got space', $builder->path);
    }

    public function testPort()
    {
        $builder = new Builder;
        // default value
        $this->assertSame(0, $builder->port);
        // straight assignment value
        $builder->port = 443;
        $this->assertSame(443, $builder->port);
        // function assignment value
        $return = $builder->port(8000);
        $this->assertSame(8000, $builder->port);
        $this->assertSame($builder, $return);
        // invalid port
        $this->expectException(\Exception::class);
        $builder->port(-1);
        $this->expectException(\Exception::class);
        $builder->port = -1;
        $this->expectException(\Exception::class);
        $builder->port(70000);
        $this->expectException(\Exception::class);
        $builder->port = 70000;
    }

    public function testPath()
    {
        $builder = new Builder;
        // default value
        $this->assertSame('', $builder->path);
        // straight assignment value
        $builder->path = 'straight_assignment_path';
        $this->assertSame('straight_assignment_path', $builder->path);
        // function assignment value
        $return = $builder->path('funciton_assignment_path');
        $this->assertSame('funciton_assignment_path', $builder->path);
        $this->assertSame($builder, $return);
        // function mode
        // replace mode
        $return = $builder->path('/replace_path', 'replace');
        $this->assertSame('/replace_path', $builder->path);
        $this->assertSame($builder, $return);
        // add mode
        $return = $builder->path('/add_path', 'add');
        $this->assertSame('/replace_path/add_path', $builder->path);
        $this->assertSame($builder, $return);
        // exception mode
        $this->expectException(\Exception::class);
        $builder->path('/add_path', 'invalid_mode_string');
    }

    public function testQuery()
    {
        $builder = new Builder;
        // default value
        $this->assertSame([], $builder->queries);
        // straight assignment value
        $builder->queries = ['test' => 'test'];
        $this->assertSame(['test' => 'test'], $builder->queries);
        // function assignment value
        $return = $builder->queries(['test_function_assignment' => 'test_function_assignment']);
        $this->assertSame(['test_function_assignment' => 'test_function_assignment'], $builder->queries);
        $this->assertSame($builder, $return);
        // function mode
        // replace mode
        $return = $builder->queries(['test_replace_mode' => 'test_replace_mode'], 'replace');
        $this->assertSame(['test_replace_mode' => 'test_replace_mode'], $builder->queries);
        $this->assertSame($builder, $return);
        // add mode
        $return = $builder->queries(['test_add_mode' => 'test_add_mode'], 'add');
        $this->assertSame([
            'test_replace_mode' => 'test_replace_mode',
            'test_add_mode' => 'test_add_mode'
        ], $builder->queries);
        $this->assertSame($builder, $return);
        // exception mode
        $this->expectException(\Exception::class);
        $builder->queries(['test_add_mode' => 'test_add_mode'], 'invalid_mode_string');

        // query function
        $return = $builder->query('test_query_func', 'test_query_func');
        $this->assertSame([
            'test_replace_mode' => 'test_replace_mode',
            'test_add_mode' => 'test_add_mode',
            'test_query_func' => 'test_query_func'
        ], $builder->queries);
        $this->assertSame($builder, $return);
    }

    public function testOpt()
    {
        $builder = new Builder;
        // default value
        $this->assertSame([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ], $builder->opts);
        // straight assignment value
        $builder->opts = [CURLOPT_HTTPGET => true];
        $this->assertSame([CURLOPT_HTTPGET => true], $builder->opts);
        // function assignment value
        $return = $builder->opts([CURLOPT_RETURNTRANSFER => true]);
        $this->assertSame([CURLOPT_RETURNTRANSFER => true], $builder->opts);
        $this->assertSame($builder, $return);
        // function mode
        // replace mode
        $return = $builder->opts([CURLOPT_TIMEOUT => 10], 'replace');
        $this->assertSame([CURLOPT_TIMEOUT => 10], $builder->opts);
        $this->assertSame($builder, $return);
        // add mode
        $return = $builder->opts([CURLOPT_RETURNTRANSFER => true], 'add');
        $this->assertArrayHasKey(CURLOPT_RETURNTRANSFER, $builder->opts);
        $this->assertArrayHasKey(CURLOPT_TIMEOUT, $builder->opts);
        $this->assertSame(true, $builder->opts[CURLOPT_RETURNTRANSFER]);
        $this->assertSame(10, $builder->opts[CURLOPT_TIMEOUT]);
        $this->assertSame($builder, $return);
        // exception mode
        $this->expectException(\Exception::class);
        $builder->opts([CURLOPT_HTTPGET => true], 'invalid_mode_string');
        // key not int
        $this->expectException(\Exception::class);
        $builder->opts(['key_not_int' => true]);

        // opt function
        $return = $builder->opt(CURLOPT_HTTPGET, true);
        $this->assertArrayHasKey(CURLOPT_RETURNTRANSFER, $builder->opts);
        $this->assertArrayHasKey(CURLOPT_TIMEOUT, $builder->opts);
        $this->assertArrayHasKey(CURLOPT_HTTPGET, $builder->opts);
        $this->assertSame(true, $builder->opts[CURLOPT_RETURNTRANSFER]);
        $this->assertSame(10, $builder->opts[CURLOPT_TIMEOUT]);
        $this->assertSame(true, $builder->opts[CURLOPT_HTTPGET]);
        $this->assertSame($builder, $return);
    }

    public function testUrl()
    {
        // http
        $builder = new Builder;
        $return = $builder->url('http://www.example.com:8000/path?query=string#fragment');
        $this->assertSame($builder, $return);
        $this->assertSame('http', $builder->scheme);
        $this->assertSame('www.example.com', $builder->host);
        $this->assertSame(8000, $builder->port);
        $this->assertSame('/path', $builder->path);
        $this->assertSame('fragment', $builder->fragment);
        $this->assertSame(['query' => 'string'], $builder->queries);

        // ftp or else
        $builder = new Builder;
        $return = $builder->url('ftp://username:password@www.example.com/path');
        $this->assertSame($builder, $return);
        $this->assertSame('ftp', $builder->scheme);
        $this->assertSame('www.example.com', $builder->host);
        $this->assertSame('/path', $builder->path);
        $this->assertSame('username', $builder->user);
        $this->assertSame('password', $builder->pass);

        // invalid url
        $builder = new Builder;
        $this->expectException(\Exception::class);
        $return = $builder->url('http:///example.com');
    }
}
