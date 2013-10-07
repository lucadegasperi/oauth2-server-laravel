<?php

use \Mockery as m;
use LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade;

class AuthorizationServerFacadeTest extends TestCase {

    public function test_name_is_correct()
    {
        $name = ResourceServerFacade::getFacadeAccessor();
        $this->assertEquals('oauth2.authorization-server', $name);
    }
}