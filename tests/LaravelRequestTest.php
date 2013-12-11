<?php

use \Mockery as m;
use LucaDegasperi\OAuth2Server\Util\LaravelRequest;

class LaravelRequestTest extends TestCase {

    protected $mockInput;

    public function setUp() {
        $this->mockInput = Input::shouldReceive('all')->once()->andReturn(array('all' => 'bar'));
        $this->mockInput->shouldReceive('file')->once()->andReturn(array('all' => 'bar'));
        $this->mockInput->shouldReceive('cookie')->once()->andReturn(array('all' => 'bar'));
    }

    public function call_method($method)
    {
        $request = new LaravelRequest();

        $result1 = $request->{$method}();
        $result2 = $request->{$method}('all');
        $result3 = $request->{$method}('none', 'baz');

        $this->assertArrayHasKey('all', $result1);
        $this->assertEquals('bar', $result2);
        $this->assertEquals('baz', $result3);
    }

    public function test_get()
    {
        $this->call_method('get');
    }

    public function test_post()
    {
        $this->call_method('post');
    }

    public function test_put()
    {
        $this->call_method('put');
    }

    public function test_delete()
    {
        $this->call_method('delete');
    }

    public function test_cookie()
    {
        $this->call_method('cookie');
    }

    public function test_file()
    {
        $this->call_method('file');
    }

    public function tearDown() {
        m::close();
    }

}