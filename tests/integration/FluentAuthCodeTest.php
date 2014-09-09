<?php

use LucaDegasperi\OAuth2Server\Storage\FluentAuthCode;
use Mockery as m;

class FluentAuthCodeTest extends DBTestCase
{
    public function getAuthCodeRepository()
    {
        $server = m::mock('League\OAuth2\Server\AbstractServer');
        $repo = new FluentAuthCode($this->app['db']);
        $repo->setServer($server);

        return $repo;
    }

    public function test_it_fetches_an_auth_code_with_a_valid_code()
    {
        $repo = $this->getAuthCodeRepository();

        $result = $repo->get('totallyanauthcode1');

        $this->assertInstanceOf('League\OAuth2\Server\Entity\AuthCodeEntity', $result);
        $this->assertEquals('totallyanauthcode1', $result->getId());
        $this->assertInternalType('int', $result->getExpireTime());
    }

    public function test_it_returns_null_with_an_invalid_code()
    {
        $repo = $this->getAuthCodeRepository();

        $result = $repo->get('invalid_auth_code');

        $this->assertNull($result);
    }

    public function test_it_deletes_an_auth_code()
    {
        $code = m::mock('League\OAuth2\Server\Entity\AuthCodeEntity');
        $code->shouldReceive('getId')->once()->andReturn('totallyanauthcode1');

        $repo = $this->getAuthCodeRepository();

        $repo->delete($code);
        $result = $repo->get('totallyanauthcode1');

        $this->assertNull($result);
    }

    public function test_it_associates_scopes()
    {
        $code = m::mock('League\OAuth2\Server\Entity\AuthCodeEntity');
        $code->shouldReceive('getId')->times(4)->andReturn('totallyanauthcode1');

        $scope1 = m::mock('League\OAuth2\Server\Entity\ScopeEntity');
        $scope1->shouldReceive('getId')->once()->andReturn('scope1');

        $scope2 = m::mock('League\OAuth2\Server\Entity\ScopeEntity');
        $scope2->shouldReceive('getId')->once()->andReturn('scope2');

        $repo = $this->getAuthCodeRepository();

        $result1 = $repo->getScopes($code);

        $repo->associateScope($code, $scope1);
        $repo->associateScope($code, $scope1);

        $result2 = $repo->getScopes($code);

        $this->assertInternalType('array', $result1);
        $this->assertEquals(0, count($result1));

        $this->assertInternalType('array', $result2);
        $this->assertEquals(2, count($result2));

        $first = $result2[0];

        $this->assertInstanceOf('League\OAuth2\Server\Entity\ScopeEntity', $first);
        $this->assertEquals('scope1', $first->getId());
    }

    public function test_it_creates_an_auth_code()
    {
        $repo = $this->getAuthCodeRepository();

        $time = time() + 120;
        $repo->create('newauthcode', $time, 1, 'http://example1.com');
        $result = $repo->get('newauthcode');

        $this->assertInstanceOf('League\OAuth2\Server\Entity\AuthCodeEntity', $result);
        $this->assertEquals('newauthcode', $result->getId());
        $this->assertInternalType('int', $result->getExpireTime());
        $this->assertEquals($time, $result->getExpireTime());
        $this->assertEquals('http://example1.com', $result->getRedirectUri());
    }
}
