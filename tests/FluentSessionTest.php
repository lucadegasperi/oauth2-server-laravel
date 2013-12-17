<?php

use LucaDegasperi\OAuth2Server\Repositories\FluentSession;

class FluentSessionTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->setUpDb();   
    }

    public function teardown()
    {
        $this->teardownDb();
    }

    public function test_session_is_created()
    {
        $repo = new FluentSession();

        $id = $repo->createSession('client1id', 'user', '1');

        $session = (array) DB::table('oauth_sessions')->where('id', '=', $id)->first();

        $this->assertArrayHasKey('id', $session);
        $this->assertArrayHasKey('client_id', $session);
        $this->assertArrayHasKey('owner_type', $session);
        $this->assertArrayHasKey('owner_id', $session);
        $this->assertArrayHasKey('created_at', $session);
        $this->assertArrayHasKey('updated_at', $session);

        $this->assertEquals('client1id', $session['client_id']);
        $this->assertEquals('user', $session['owner_type']);
        $this->assertEquals('1', $session['owner_id']);
    }

    public function test_session_is_deleted()
    {
        $repo = new FluentSession();

        $repo->deleteSession('client1id', 'user', '1');

        $session = DB::table('oauth_sessions')
                    ->where('client_id', '=', 'client1id')
                    ->where('owner_type', '=', 'user')
                    ->where('owner_id', '=', '1')
                    ->first();

        $this->assertNull($session, 'no session found');
    }
}