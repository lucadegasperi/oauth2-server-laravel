<?php

use \Mockery as m;
use LucaDegasperi\OAuth2Server\Proxies\AuthorizationServerProxy;

class AuthorizationServerProxyTest extends TestCase {

    public function getProxy($mock)
    {
        return new AuthorizationServerProxy($mock);
    }

    public function getMock()
    {
        return m::mock('League\OAuth2\Server\Authorization');
    }

    public function getStub()
    {
        return array(
            'client_id' => 1,
            'client_details' => 'foo',
            'redirect_uri' => 'http://www.example.com/',
            'response_type' => 'code',
            'scopes' => 'scope',
            'state' => '123456789',
        );
    }

    public function test_make_redirect()
    {
        $proxy = $this->getProxy($this->getMock());

        $result = $proxy->makeRedirect('example');

        $this->assertEquals('example?', $result);
    }

    public function test_check_authorize_params()
    {
        $mock = $this->getMock();
        $mock->shouldReceive('getGrantType->checkAuthoriseParams')->andReturn($this->getStub());

        $response = $this->getProxy($mock)->checkAuthorizeParams();

        $this->assertEquals($this->getStub(), $response);
    }

    public function test_access_token_correctly_issued()
    {
        $mock = $this->getMock();
        $mock->shouldReceive('issueAccessToken')->once()->andReturn(array('foo' => 'bar'));

        $response = $this->getProxy($mock)->performAccessTokenFlow();

        $this->assertEquals('{"foo":"bar"}', $response->getContent());
        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isOk());
    }

    public function test_access_token_with_client_error()
    {
        $mock = $this->getMock();
        $mock->shouldReceive('issueAccessToken')->once()->andThrow(new League\OAuth2\Server\Exception\ClientException('client exception'));
        $mock->shouldReceive('getExceptionType')->twice()->andReturn('foo');
        $mock->shouldReceive('getExceptionHttpHeaders')->once()->andReturn(array());

        $response = $this->getProxy($mock)->performAccessTokenFlow();

        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isOk());
        
    }

    public function test_access_token_with_generic_error()
    {
        $mock = $this->getMock();
        $mock->shouldReceive('issueAccessToken')->once()->andThrow(new Exception('internal server error'));

        $response = $this->getProxy($mock)->performAccessTokenFlow();

        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isServerError());
        
    }

    public function test_calls_to_underlying_object()
    {
        $mock = $this->getMock();
        $mock->shouldReceive('unexistingMethod')->times(6)->andReturn('baz');

        $proxy = $this->getProxy($mock);
        $responses = array();
        $responses[] = $proxy->unexistingMethod();
        $responses[] = $proxy->unexistingMethod('foo');
        $responses[] = $proxy->unexistingMethod('foo', 'bar');
        $responses[] = $proxy->unexistingMethod('foo', 'bar', 'foo');
        $responses[] = $proxy->unexistingMethod('foo', 'bar', 'foo', 'bar');
        $responses[] = $proxy->unexistingMethod('foo', 'bar', 'foo', 'bar', 'foo');

        for($i = 0; $i < count($responses); $i++)
        {
            $this->assertEquals('baz', $responses[$i]);
        }
    }

    public function tearDown() {
        m::close();
    }

}