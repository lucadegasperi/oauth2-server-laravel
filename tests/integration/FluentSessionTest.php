<?php

use LucaDegasperi\OAuth2Server\Repositories\FluentSession;
use Mockery as m;

class FluentSessionTest extends DbTestCase
{
    public function getSessionRepository()
    {
        $server = m::mock('League\OAuth2\Server\AbstractServer');
        $repo = new FluentSession();
        $repo->setServer($server);

        return $repo;
    }

    public function test_session_is_created()
    {
        $repo = $this->getSessionRepository();

        $id = $repo->create('user', '1', 'client1');
        $session = $repo->get($id);

        $this->assertInstanceOf('League\OAuth2\Server\Entity\Session', $session);
        $this->assertEquals('user', $session->getOwnerType());
        $this->assertEquals('1', $session->getOwnerId());
    }

    /*public function test_session_is_deleted()
    {
        $repo = new FluentSession();

        $repo->delete('client1id', 'user', '1');

        $session = DB::table('oauth_sessions')
                    ->where('client_id', '=', 'client1id')
                    ->where('owner_type', '=', 'user')
                    ->where('owner_id', '=', '1')
                    ->first();

        $this->assertNull($session, 'no session found');
    }*/
}
