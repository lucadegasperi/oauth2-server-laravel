<?php

use \Mockery as m;
use LucaDegasperi\OAuth2Server\Proxies\AuthorizationServerProxy;

class AuthorizationServerProxyTest extends TestCase {

    public function getProxy($mock)
    {
        return new AuthorizationServerProxy($mock);
    }

    public function getServer()
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
        $proxy = $this->getProxy($this->getServer());

        $result = $proxy->makeRedirect('example');

        $this->assertEquals('example?', $result);
    }

    public function test_make_redirect_with_code()
    {
        $proxy = $this->getProxy($this->getServer());

        $result = $proxy->makeRedirectWithCode('1234567890', array('redirect_uri' => 'example'));

        $this->assertEquals('example?code=1234567890&state=', $result);

        $result = $proxy->makeRedirectWithCode('1234567890', array('redirect_uri' => 'example', 'state' => 'random'));

        $this->assertEquals('example?code=1234567890&state=random', $result);
    }

    public function test_make_redirect_with_error()
    {
        $mock = $this->getServer();
        $mock->shouldReceive('getExceptionMessage')->twice()->andReturn('error_message');

        $proxy = $this->getProxy($mock);

        $result = $proxy->makeRedirectWithError(array('redirect_uri' => 'example'));

        $this->assertEquals('example?error=access_denied&error_message=error_message&state=', $result);

        $result = $proxy->makeRedirectWithError(array('redirect_uri' => 'example', 'state' => 'random'));

        $this->assertEquals('example?error=access_denied&error_message=error_message&state=random', $result);
    }

    public function test_check_authorize_params()
    {
        $mock = $this->getServer();
        $mock->shouldReceive('getGrantType->checkAuthoriseParams')->andReturn($this->getStub());

        $response = $this->getProxy($mock)->checkAuthorizeParams();

        $this->assertEquals($this->getStub(), $response);
    }

    public function test_new_authorize_request()
    {
        $mock = $this->getServer();
        $mock->shouldReceive('getGrantType->newAuthoriseRequest')->andReturn('example_code');

        $response = $this->getProxy($mock)->newAuthorizeRequest('user', 1, $this->getStub());

        $this->assertEquals('example_code', $response);
    }

    public function test_access_token_correctly_issued()
    {
        $mock = $this->getServer();
        $mock->shouldReceive('issueAccessToken')->once()->andReturn(array('foo' => 'bar'));

        $response = $this->getProxy($mock)->performAccessTokenFlow();

        $this->assertEquals('{"foo":"bar"}', $response->getContent());
        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isOk());
    }

    public function test_access_token_with_client_error()
    {
        $mock = $this->getServer();
        $mock->shouldReceive('issueAccessToken')->once()->andThrow(new League\OAuth2\Server\Exception\ClientException('client exception'));
        $mock->shouldReceive('getExceptionType')->twice()->andReturn('foo');
        $mock->shouldReceive('getExceptionHttpHeaders')->once()->andReturn(array());

        $response = $this->getProxy($mock)->performAccessTokenFlow();

        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isOk());
        
    }

    public function test_access_token_with_generic_error()
    {
        $mock = $this->getServer();
        $mock->shouldReceive('issueAccessToken')->once()->andThrow(new Exception('internal server error'));

        $response = $this->getProxy($mock)->performAccessTokenFlow();

        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isServerError());
        
    }

    public function test_calls_to_underlying_object()
    {
        $mock = $this->getServer();
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