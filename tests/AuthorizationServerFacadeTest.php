<?php

use \Mockery as m;
use LucaDegasperi\OAuth2Server\Facades\AuthorizationServerFacade;
use League\OAuth2\Server\Util\RedirectUri;

class AuthorizationServerFacadeTest extends TestCase {

    public function test_make_redirect()
    {
        $redirect = AuthorizationServerFacade::makeRedirect('example');

        $this->assertEquals('example?', $redirect);
    }

    public function tearDown() {
        m::close();
    }

}