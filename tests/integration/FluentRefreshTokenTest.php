<?php

use LucaDegasperi\OAuth2Server\Storage\FluentRefreshToken;
use Mockery as m;

class FluentRefreshTokenTest extends DBTestCase
{
    public function getRefreshTokenRepository()
    {
        $server = m::mock('League\OAuth2\Server\AbstractServer');
        $repo = new FluentRefreshToken($this->app['db']);
        $repo->setServer($server);

        return $repo;
    }

    public function test_it_fetches_a_refresh_token_with_a_valid_token()
    {
        $repo = $this->getRefreshTokenRepository();

        $result = $repo->get('totallyarefreshtoken1');

        $this->assertInstanceOf('League\OAuth2\Server\Entity\RefreshTokenEntity', $result);
        $this->assertEquals('totallyarefreshtoken1', $result->getId());
        $this->assertInternalType('int', $result->getExpireTime());
    }

    public function test_it_returns_null_with_an_invalid_token()
    {
        $repo = $this->getRefreshTokenRepository();

        $result = $repo->get('invalid_refresh_token');

        $this->assertNull($result);
    }

    public function test_it_deletes_a_refresh_token()
    {
        $token = m::mock('League\OAuth2\Server\Entity\RefreshTokenEntity');
        $token->shouldReceive('getId')->once()->andReturn('totallyarefreshtoken1');

        $repo = $this->getRefreshTokenRepository();

        $repo->delete($token);
        $result = $repo->get('totallyarefreshtoken1');

        $this->assertNull($result);
    }

    public function test_it_creates_a_refresh_token()
    {
        $repo = $this->getRefreshTokenRepository();

        $time = time() + 120;
        $result = $repo->create('newrefreshtoken', $time, 'totallyanaccesstoken2');

        $this->assertInstanceOf('League\OAuth2\Server\Entity\RefreshTokenEntity', $result);
        $this->assertEquals('newrefreshtoken', $result->getId());
        $this->assertInternalType('int', $result->getExpireTime());
        $this->assertEquals($time, $result->getExpireTime());
    }
}
