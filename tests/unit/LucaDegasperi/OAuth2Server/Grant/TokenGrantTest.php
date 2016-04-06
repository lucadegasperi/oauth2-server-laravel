<?php

namespace LeagueTests\Grant;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use LucaDegasperi\OAuth2Server\Grant\TokenGrant;
use Mockery as M;

class TokenGrantTest extends \PHPUnit_Framework_TestCase
{
    public function testSetRefreshTokenTTL()
    {
        $grant = new TokenGrant();
        $grant->setAccessTokenTTL(86400);

        $property = new \ReflectionProperty($grant, 'accessTokenTTL');
        $property->setAccessible(true);

        $this->assertEquals(86400, $property->getValue($grant));
    }

    public function testCompleteFlowMissingClientId()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST['grant_type'] = 'token';

        $server = new AuthorizationServer();
        $grant = new TokenGrant();

        $server->addGrantType($grant,'token');
        $server->issueAccessToken();
    }

    public function testCompleteFlowMissingClientSecret()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST = [
            'grant_type' => 'token',
            'client_id'  =>  'testapp',
        ];

        $_SERVER = [
            'http_Authorization' => 'Bearer 74831438430248973184734'
        ];

        $server = new AuthorizationServer();
        $grant = new TokenGrant();

        $server->addGrantType($grant,'token');
        $server->issueAccessToken();
    }

    public function testCompleteFlowInvalidClient()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidClientException');

        $_POST = [
            'grant_type' => 'token',
            'client_id' =>  'testapp',
            'client_secret' =>  'foobar',
        ];

        $server = new AuthorizationServer();
        $grant = new TokenGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(null);

        $server->setClientStorage($clientStorage);

        $server->addGrantType($grant,'token');
        $server->issueAccessToken();
    }

    public function testCompleteFlowMissingToken()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\AccessDeniedException');

        $_POST = [
            'grant_type'    => 'token',
            'client_id'     =>  'testapp',
            'client_secret' =>  'foobar',
        ];

        $_SERVER = [];

        $server = new AuthorizationServer();
        $grant = new TokenGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->requireScopeParam(true);

        $server->addGrantType($grant,'token');
        $server->issueAccessToken();
    }

    public function testCompleteFlowInvalidToken()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\AccessDeniedException');

        $_POST = [
            'grant_type'    => 'token',
            'client_id'     =>  'testapp',
            'client_secret' =>  'foobar',
        ];

        $_SERVER = [
            'HTTP_AUTHORIZATION'    => 'Bearer 4637648641763471634736147123423'
        ];

        $server = new AuthorizationServer();
        $grant = new TokenGrant();

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('get')->andReturn(
            null
        );
        $accessTokenStorage->shouldReceive('delete');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->requireScopeParam(true);
        $server->setAccessTokenStorage($accessTokenStorage);

        $server->addGrantType($grant,'token');
        $server->issueAccessToken();
    }

    public function testCompleteFlowExpiredToken()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\AccessDeniedException');

        $_POST = [
            'grant_type'    => 'token',
            'client_id'     =>  'testapp',
            'client_secret' =>  'foobar',
        ];

        $_SERVER = [
            'HTTP_AUTHORIZATION'    => 'Bearer 4637648641763471634736147123423'
        ];

        $server = new AuthorizationServer();
        $grant = new TokenGrant();

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('get')->andReturn(
            (new AccessTokenEntity($server))->setExpireTime(time() - 1000)
        );
        $accessTokenStorage->shouldReceive('delete');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->requireScopeParam(true);
        $server->setAccessTokenStorage($accessTokenStorage);

        $server->addGrantType($grant,'token');
        $server->issueAccessToken();
    }

    public function testCompleteFlowExistingScopes()
    {
        //$this->setExpectedException('League\OAuth2\Server\Exception\AccessDeniedException');

        $_POST = [
            'grant_type'    => 'token',
            'client_id'     =>  'testapp',
            'client_secret' =>  'foobar',
            'scope'         =>  'foo'
        ];

        $_SERVER = [
            'HTTP_AUTHORIZATION'    => 'Bearer 4637648641763471634736147123423'
        ];

        $server = new AuthorizationServer();
        $grant = new TokenGrant();

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');
        $sessionStorage->shouldReceive('getScopes')->shouldReceive('getScopes')->andReturn([]);
        $sessionStorage->shouldReceive('associateScope');
        $sessionStorage->shouldReceive('getByAccessToken')->andReturn(
            (new SessionEntity($server))
        );

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('get')->andReturn(
            (new AccessTokenEntity($server))->setExpireTime(time() + 3700)
        );
        $accessTokenStorage->shouldReceive('delete');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');
        $scopeStorage->shouldReceive('get')->andReturn(
            (new ScopeEntity($server))->hydrate(['id' => 'foo'])
        );

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->requireScopeParam(true);
        $server->setAccessTokenStorage($accessTokenStorage);

        $server->addGrantType($grant,'token');
        $response = $server->issueAccessToken();

        $this->assertTrue(array_key_exists('access_token', $response));
        $this->assertTrue(array_key_exists('token_type', $response));
        $this->assertTrue(array_key_exists('expires_in', $response));
    }
}
