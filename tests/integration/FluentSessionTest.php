<?php

use LucaDegasperi\OAuth2Server\Storage\FluentSession;
use Mockery as m;

class FluentSessionTest extends DbTestCase
{
    public function getSessionRepository()
    {
        $emitter = m::mock('League\Event\Emitter');
        $emitter->shouldReceive('emit')->once();
        $server = m::mock('League\OAuth2\Server\AbstractServer');
        $server->shouldReceive('getEventEmitter')->once()->andReturn($emitter);
        $repo = new FluentSession($this->app['db']);
        $repo->setServer($server);

        return $repo;
    }

    public function test_session_is_created()
    {
        $repo = $this->getSessionRepository();

        $id = $repo->create('user', '1', 'client1');
        $session = $repo->get($id);

        $this->assertInstanceOf('League\OAuth2\Server\Entity\SessionEntity', $session);
        $this->assertEquals('user', $session->getOwnerType());
        $this->assertEquals('1', $session->getOwnerId());
    }

    public function test_null_is_returned_when_invalid_session_is_requested()
    {
        $repo = $this->getSessionRepository();
        $session = $repo->get(20);
        $this->assertNull($session);
    }

    public function test_scope_is_associated()
    {
        $session = m::mock('League\OAuth2\Server\Entity\SessionEntity');
        $session->shouldReceive('getId')->twice()->andReturn(1);

        $scope1 = m::mock('League\OAuth2\Server\Entity\ScopeEntity');
        $scope1->shouldReceive('getId')->once()->andReturn('scope1');

        $scope2 = m::mock('League\OAuth2\Server\Entity\ScopeEntity');
        $scope2->shouldReceive('getId')->once()->andReturn('scope2');

        $repo = $this->getSessionRepository();

        $repo->associateScope($session, $scope1);
        $repo->associateScope($session, $scope2);

        $result = $repo->getScopes($session);

        $this->assertInternalType('array', $result);
        $this->assertEquals(2, count($result));
        $first = $result[0];
        $this->assertInstanceOf('League\OAuth2\Server\Entity\ScopeEntity', $first);
    }

    public function test_null_is_returned_when_session_is_requested_by_invalid_auth_code()
    {
        $authCode = m::mock('League\OAuth2\Server\Entity\AuthCodeEntity');
        $authCode->shouldReceive('getId')->once()->andReturn('unexistingcode');

        $repo = $this->getSessionRepository();

        $result = $repo->getByAuthCode($authCode);

        $this->assertNull($result);
    }

    public function test_a_session_is_returned_when_session_is_requested_by_valid_auth_code()
    {
        $authCode = m::mock('League\OAuth2\Server\Entity\AuthCodeEntity');
        $authCode->shouldReceive('getId')->once()->andReturn('totallyanauthcode1');

        $repo = $this->getSessionRepository();

        $session = $repo->getByAuthCode($authCode);

        $this->assertInstanceOf('League\OAuth2\Server\Entity\SessionEntity', $session);
        $this->assertEquals('user', $session->getOwnerType());
        $this->assertEquals('1', $session->getOwnerId());
    }

    public function test_null_is_returned_when_session_is_requested_by_invalid_access_token()
    {
        $accessToken = m::mock('League\OAuth2\Server\Entity\AccessTokenEntity');
        $accessToken->shouldReceive('getId')->once()->andReturn('unexistingaccesstoken');

        $repo = $this->getSessionRepository();

        $result = $repo->getByAccessToken($accessToken);

        $this->assertNull($result);
    }

    public function test_a_session_is_returned_when_session_is_requested_by_valid_access_token()
    {
        $accessToken = m::mock('League\OAuth2\Server\Entity\AccessTokenEntity');
        $accessToken->shouldReceive('getId')->once()->andReturn('totallyanaccesstoken1');

        $repo = $this->getSessionRepository();

        $session = $repo->getByAccessToken($accessToken);

        $this->assertInstanceOf('League\OAuth2\Server\Entity\SessionEntity', $session);
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
