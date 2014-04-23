<?php

use \Mockery as m;
use LucaDegasperi\OAuth2Server\Decorators\AuthorizationServerDecorator;

class AuthorizationServerDecoratorTest extends TestCase
{

    public function getDecorator($mock)
    {
        return new AuthorizationServerDecorator($mock);
    }

    public function getServer()
    {
        return m::mock('League\OAuth2\Server\AuthorizationServer');
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
        // arrange
        $decorator = $this->getDecorator($this->getServer());

        // act
        $result = $decorator->makeRedirect('example');

        // assert
        $this->assertEquals('example?', $result);
    }

    public function test_make_redirect_with_code()
    {
        // arrange
        $decorator = $this->getDecorator($this->getServer());

        // act
        $result1 = $decorator->makeRedirectWithCode('1234567890', array('redirect_uri' => 'example'));
        $result2 = $decorator->makeRedirectWithCode('1234567890', array('redirect_uri' => 'example', 'state' => 'random'));

        // assert
        $this->assertEquals('example?code=1234567890&state=', $result1);
        $this->assertEquals('example?code=1234567890&state=random', $result2);
    }

    public function test_make_redirect_with_error()
    {
        // arrange
        $server = $this->getServer();
        $decorator = $this->getDecorator($server);

        // act
        $result1 = $decorator->makeRedirectWithError(array('redirect_uri' => 'example'));
        $result2 = $decorator->makeRedirectWithError(array('redirect_uri' => 'example', 'state' => 'random'));

        // assert
        $this->assertEquals('example?error=access_denied&error_message=The+resource+owner+or+authorization+server+denied+the+request.&state=', $result1);
        $this->assertEquals('example?error=access_denied&error_message=The+resource+owner+or+authorization+server+denied+the+request.&state=random', $result2);
    }

    public function test_check_authorize_params()
    {
        // arrange
        $grant = m::mock();
        $grant->shouldReceive('checkAuthoriseParams')->once()->andReturn($this->getStub());

        $server = $this->getServer();
        $server->shouldReceive('getGrantType')->once()->andReturn($grant);

        // act
        $response = $this->getDecorator($server)->checkAuthorizeParams();

        // assert
        $this->assertEquals($this->getStub(), $response);
    }

    public function test_new_authorize_request()
    {
        // arrange
        $grant = m::mock();
        $grant->shouldReceive('newAuthoriseRequest')->once()->andReturn('example_code');

        $server = $this->getServer();
        $server->shouldReceive('getGrantType')->with('authorization_code')->once()->andReturn($grant);

        // act
        $response = $this->getDecorator($server)->newAuthorizeRequest('user', 1, $this->getStub());

        // assert
        $this->assertEquals('example_code', $response);
    }

    public function test_access_token_correctly_issued()
    {
        // arrange
        $mock = $this->getServer();
        $mock->shouldReceive('issueAccessToken')->once()->andReturn(array('foo' => 'bar'));

        // act
        $response = $this->getDecorator($mock)->performAccessTokenFlow();

        // assert
        $this->assertEquals('{"foo":"bar"}', $response->getContent());
        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isOk());
    }

    public function test_access_token_with_client_error()
    {
        // arrange
        $server = $this->getServer();
        $server->shouldReceive('issueAccessToken')->once()->andThrow(new League\OAuth2\Server\Exception\ClientException('client exception'));

        // act
        $response = $this->getDecorator($server)->performAccessTokenFlow();

        // assert
        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertFalse($response->isOk());
    }

    public function test_access_token_with_generic_error()
    {
        // arrange
        $mock = $this->getServer();
        $mock->shouldReceive('issueAccessToken')->once()->andThrow(new Exception('internal server error'));

        // act
        $response = $this->getDecorator($mock)->performAccessTokenFlow();

        // assert
        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isServerError());
    }

    public function tearDown() {
        m::close();
    }

}