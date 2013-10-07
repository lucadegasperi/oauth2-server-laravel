<?php

use \Mockery as m;

class OAuthOwnerFilterTest extends TestCase {

    public function getFilter()
    {
        return new LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter;
    }

    public function test_with_existing_owner_type()
    {
        ResourceServer::shouldReceive('getOwnerType')->once()->andReturn('foo');

        $response = $this->getFilter()->filter('', '', 'foo');
        $this->assertNull($response);
    }

    public function test_with_unexisting_owner_type()
    {
        ResourceServer::shouldReceive('getOwnerType')->once()->andReturn('foo');

        $response = $this->getFilter()->filter('', '', 'bar');
        $this->assertTrue($response instanceof Illuminate\Http\JsonResponse);
        $this->assertTrue($response->isForbidden());
    }

    public function tearDown() {
        m::close();
    }

}