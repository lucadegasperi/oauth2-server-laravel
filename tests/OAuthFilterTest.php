<?php

use \Mockery as m;

class OAuthFilterTest extends TestCase {

    public function getFilter()
    {
        return new LucaDegasperi\OAuth2Server\Filters\OAuthFilter;
    }

    public function test_valid_filter_with_no_scope()
    {
        ResourceServer::shouldReceive('isValid')->once()->andReturn(true);

        $response = $this->getFilter()->filter('', '', null);
        $this->assertNull($response);
    }

    public function test_invalid_filter_with_no_scope()
    {
        //$e = m::mock();
        //$exception->shouldReceive('getMessage')->once()->andReturn('foo error message');

        ResourceServer::shouldReceive('isValid')->andThrow(new \League\OAuth2\Server\Exception\InvalidAccessTokenException('Access token is not valid'));

        $response = $this->getFilter()->filter('', '', null);
        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);

        // to check te status code and the json payload

    }

    public function tearDown() {
        m::close();
    }

}