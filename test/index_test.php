<?php

class index_test extends PHPUnit_Framework_TestCase {

    public function testA() {
        $this->assertEquals("a", "a");
    }

    public function testB() {
        $str = null;
        $this->assertEmpty($str);
    }

}
