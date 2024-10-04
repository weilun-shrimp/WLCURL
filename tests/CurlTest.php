<?php

namespace Weilun\WLCURL\Tests;

use PHPUnit\Framework\TestCase;
use WeiLun\WLCURL\Curl;

class CurlTest extends TestCase
{
    /**
     * scheme, user, pass, host, fragment
     */
    public function testStraightStringAttributes()
    {
        $curl = new Curl;
        foreach (['scheme', 'user', 'pass', 'host', 'fragment'] as $v) {
            // default value
            $this->assertSame('', $curl->{$v});
            // straight assignment value
            $curl->{$v} = "straight_assignment_$v";
            $this->assertSame("straight_assignment_$v", $curl->{$v});
            // function assignment value
            $curl->{$v}("funciton_assignment_$v");
            $this->assertSame("funciton_assignment_$v", $curl->{$v});
        }
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
        $curl->path('funciton_assignment_path');
        $this->assertSame('funciton_assignment_path', $curl->path);
        // function mode
        // replace mode
        $curl->path('/replace_path', 'replace');
        $this->assertSame('/replace_path', $curl->path);
        // add mode
        $curl->path('/add_path', 'add');
        $this->assertSame('/replace_path/add_path', $curl->path);
    }
}
