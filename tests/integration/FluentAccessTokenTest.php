<?php

use LucaDegasperi\OAuth2Server\Storage\FluentAccessToken;
use Mockery as m;

class FluentAccessTokenTest extends DBTestCase
{
    public function getAccessTokenRepository()
    {
        $server = m::mock('League\OAuth2\Server\AbstractServer');
        $repo = new FluentAccessToken($this->app['db']);
        $repo->setServer($server);

        return $repo;
    }

    public function test_it_fetches_an_access_token_object_with_a_valid_token()
    {
        $repo = $this->getAccessTokenRepository();

        $result = $repo->get('totallyanaccesstoken1');

        $this->assertInstanceOf('League\OAuth2\Server\Entity\AccessTokenEntity', $result);
        $this->assertEquals('totallyanaccesstoken1', $result->getId());
        $this->assertInternalType('int', $result->getExpireTime());
    }

    public function test_it_returns_null_with_an_invalid_token()
    {
        $repo = $this->getAccessTokenRepository();

        $result = $repo->get('invalid_auth_code');

        $this->assertNull($result);
    }

    /*public function test_it_fetches_an_access_token_object_with_a_valid_refresh_token()
    {
        $token = m::mock('League\OAuth2\Server\Entity\RefreshTokenEntity');
        $token->shouldReceive('getId')->once()->andReturn('totallyarefreshtoken1');

        $repo = $this->getAccessTokenRepository();

        $result = $repo->getByRefreshToken($token);

        $this->assertInstanceOf('League\OAuth2\Server\Entity\AccessTokenEntity', $result);
        $this->assertEquals('totallyanaccesstoken1', $result->getId());
        $this->assertInternalType('int', $result->getExpireTime());
    }

    public function test_it_returns_null_with_an_invalid_refresh_token()
    {
        $token = m::mock('League\OAuth2\Server\Entity\RefreshTokenEntity');
        $token->shouldReceive('getId')->once()->andReturn('notarefreshtoken');

        $repo = $this->getAccessTokenRepository();

        $result = $repo->getByRefreshToken($token);

        $this->assertNull($result);
    }*/

    public function test_it_deletes_an_access_token()
    {
        $token = m::mock('League\OAuth2\Server\Entity\AccessTokenEntity');
        $token->shouldReceive('getId')->once()->andReturn('totallyanaccesstoken1');

        $repo = $this->getAccessTokenRepository();

        $repo->delete($token);
        $result = $repo->get('totallyanaccesstoken1');

        $this->assertNull($result);
    }

    public function test_it_associates_scopes()
    {
        $token = m::mock('League\OAuth2\Server\Entity\AccessTokenEntity');
        $token->shouldReceive('getId')->times(4)->andReturn('totallyanaccesstoken1');

        $scope1 = m::mock('League\OAuth2\Server\Entity\ScopeEntity');
        $scope1->shouldReceive('getId')->once()->andReturn('scope1');

        $scope2 = m::mock('League\OAuth2\Server\Entity\ScopeEntity');
        $scope2->shouldReceive('getId')->once()->andReturn('scope2');

        $repo = $this->getAccessTokenRepository();

        $result1 = $repo->getScopes($token);

        $repo->associateScope($token, $scope1);
        $repo->associateScope($token, $scope1);

        $result2 = $repo->getScopes($token);

        $this->assertInternalType('array', $result1);
        $this->assertEquals(0, count($result1));

        $this->assertInternalType('array', $result2);
        $this->assertEquals(2, count($result2));

        $first = $result2[0];

        $this->assertInstanceOf('League\OAuth2\Server\Entity\ScopeEntity', $first);
        $this->assertEquals('scope1', $first->getId());
    }

    public function test_it_creates_an_access_token()
    {
        $repo = $this->getAccessTokenRepository();

        $time = time() + 120;
        $result = $repo->create('accesstoken', $time, 1);

        $this->assertInstanceOf('League\OAuth2\Server\Entity\AccessTokenEntity', $result);
        $this->assertEquals('accesstoken', $result->getId());
        $this->assertInternalType('int', $result->getExpireTime());
        $this->assertEquals($time, $result->getExpireTime());
    }
}
