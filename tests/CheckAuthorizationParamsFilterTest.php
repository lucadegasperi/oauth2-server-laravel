<?php

use \Mockery as m;

class CheckAuthorizationParamsFilterTest extends TestCase {

    public function getFilter()
    {
        return new LucaDegasperi\OAuth2Server\Filters\CheckAuthorizationParamsFilter;
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

    public function test_with_valid_params()
    {
        $stub = $this->getStub();

        AuthorizationServer::shouldReceive('checkAuthorizeParams')
                           ->once()
                           ->andReturn($stub);

        Session::shouldReceive('put')->once();

        $response = $this->getFilter()->filter('','', null);

        $this->assertNull($response);
    }

    public function test_with_invalid_valid_params()
    {

        AuthorizationServer::shouldReceive('checkAuthorizeParams')
                           ->once()
                           ->andThrow(new \League\OAuth2\Server\Exception\ClientException('Invalid Request'));

        $response = $this->getFilter()->filter('','', null);

        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isClientError());
    }

    public function test_with_server_error()
    {

        AuthorizationServer::shouldReceive('checkAuthorizeParams')
                           ->once()
                           ->andThrow(new Exception('Internal Server Error'));

        $response = $this->getFilter()->filter('','', null);

        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isServerError());
    }

    public function tearDown() {
        m::close();
    }

}