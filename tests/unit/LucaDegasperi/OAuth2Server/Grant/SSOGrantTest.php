<?php

namespace LeagueTests\Grant;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;
use LucaDegasperi\OAuth2Server\Grant\SSOGrant;
use LucaDegasperi\OAuth2Server\Grant\RefreshTokenGrant;
use Mockery as M;

class SSOGrantTest extends \PHPUnit_Framework_TestCase
{
    public function testSetRefreshTokenTTL()
    {
        $grant = new SSOGrant();
        $grant->setAccessTokenTTL(86400);

        $property = new \ReflectionProperty($grant, 'accessTokenTTL');
        $property->setAccessible(true);

        $this->assertEquals(86400, $property->getValue($grant));
    }

    public function testCompleteFlowMissingClientId()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST['grant_type'] = 'sso';

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $server->addGrantType($grant,'sso');
        $server->issueAccessToken();
    }

    public function testCompleteFlowMissingClientSecret()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST = [
            'grant_type' => 'sso',
            'client_id'  =>  'testapp',
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $server->addGrantType($grant,'sso');
        $server->issueAccessToken();
    }

    public function testNoIdentity()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST = [
            'grant_type' => 'sso',
            'client_id'  =>  'testapp',
            'client_secret' =>  'foobar',
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $server->addGrantType($grant,'sso');
        $server->issueAccessToken();
    }

    public function testNoRedirectUri()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST = [
            'grant_type' => 'sso',
            'client_id'  =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'   =>  'superadmin@aristamd.com',
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $server->addGrantType($grant,'sso');
        $server->issueAccessToken();
    }

    public function testNoSignature()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST = [
            'grant_type'   => 'sso',
            'client_id'    =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'     =>  'superadmin@aristamd.com',
            'redirect_uri' => '/#/app/referrals',
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $server->addGrantType($grant,'sso');
        $server->issueAccessToken();
    }

    public function testNoCallable()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\ServerErrorException');

        $_POST = [
            'grant_type'   => 'sso',
            'client_id'    =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'     =>  'superadmin@aristamd.com',
            'redirect_uri' =>  '/#/app/referrals',
            'signature'    =>  '3ee3666123455abd48453f62fd181165',
            'scope' => 'foo'
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');
        $sessionStorage->shouldReceive('create')->andreturn(123);
        $sessionStorage->shouldReceive('getScopes')->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $sessionStorage->shouldReceive('getByAccessToken')->andReturn(
            (new SessionEntity($server))->setId('foobar')
        );
        $sessionStorage->shouldReceive('associateScope');

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');
        $scopeStorage->shouldReceive('get')->andReturn(
            (new ScopeEntity($server))->hydrate(['id' => 'foo'])
        );

        $refreshTokenStorage = M::mock('League\OAuth2\Server\Storage\RefreshTokenInterface');
        $refreshTokenStorage->shouldReceive('setServer');
        $refreshTokenStorage->shouldReceive('create');
        $refreshTokenStorage->shouldReceive('associateScope');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->setAccessTokenStorage($accessTokenStorage);
        $server->setRefreshTokenStorage($refreshTokenStorage);

        $server->addGrantType($grant, 'sso');
        $server->addGrantType(new RefreshTokenGrant());
        $response = $server->issueAccessToken();
    }

    public function testCompleteFlowInvalidScope()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidScopeException');

        $_POST = [
            'grant_type'   => 'sso',
            'client_id'    =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'     =>  'superadmin@aristamd.com',
            'redirect_uri' =>  '/#/app/referrals',
            'signature'    =>  '3ee3666123455abd48453f62fd181165',
            'scope' => 'blah'
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');
        $sessionStorage->shouldReceive('create')->andreturn(123);
        $sessionStorage->shouldReceive('getScopes')->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $sessionStorage->shouldReceive('getByAccessToken')->andReturn(
            (new SessionEntity($server))->setId('foobar')
        );
        $sessionStorage->shouldReceive('associateScope');

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');
        $scopeStorage->shouldReceive('get')->andReturn( null );

        $refreshTokenStorage = M::mock('League\OAuth2\Server\Storage\RefreshTokenInterface');
        $refreshTokenStorage->shouldReceive('setServer');
        $refreshTokenStorage->shouldReceive('create');
        $refreshTokenStorage->shouldReceive('associateScope');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->setAccessTokenStorage($accessTokenStorage);
        $server->setRefreshTokenStorage($refreshTokenStorage);

        $grant->setVerifyCredentialsCallback(function () {
            return 123;
        });

        $server->addGrantType($grant, 'sso');
        $server->addGrantType(new RefreshTokenGrant());
        $response = $server->issueAccessToken();
    }

    public function testCompleteFlowNoScopes()
    {
        //$this->setExpectedException('League\OAuth2\Server\Exception\InvalidRequestException');

        $_POST = [
            'grant_type'   => 'sso',
            'client_id'    =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'     =>  'superadmin@aristamd.com',
            'redirect_uri' =>  '/#/app/referrals',
            'signature'    =>  '3ee3666123455abd48453f62fd181165',
            'scope' => 'foo'
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');
        $sessionStorage->shouldReceive('create')->andreturn(123);
        $sessionStorage->shouldReceive('getScopes')->shouldReceive('getScopes')->andReturn([]);
        $sessionStorage->shouldReceive('getByAccessToken')->andReturn(
            (new SessionEntity($server))->setId('foobar')
        );
        $sessionStorage->shouldReceive('associateScope');

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([]);
        $accessTokenStorage->shouldReceive('associateScope');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');
        $scopeStorage->shouldReceive('get')->andReturn(
            (new ScopeEntity($server))->hydrate(['id' => 'foo'])
        );

        $refreshTokenStorage = M::mock('League\OAuth2\Server\Storage\RefreshTokenInterface');
        $refreshTokenStorage->shouldReceive('setServer');
        $refreshTokenStorage->shouldReceive('create');
        $refreshTokenStorage->shouldReceive('associateScope');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->setAccessTokenStorage($accessTokenStorage);

        $grant->setVerifyCredentialsCallback(function () {
            return 123;
        });

        $server->addGrantType($grant, 'sso');
        $response = $server->issueAccessToken();
        $this->assertTrue(array_key_exists('access_token', $response));
    }

    public function testCompleteFlowInvalidCredentials()
    {
        $this->setExpectedException('League\OAuth2\Server\Exception\InvalidCredentialsException');

        $_POST = [
            'grant_type'   => 'sso',
            'client_id'    =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'     =>  'superadmin@aristamd.com',
            'redirect_uri' =>  '/#/app/referrals',
            'signature'    =>  '3ee3666123455abd48453f62fd181165',
            'scope' => 'foo'
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');
        $sessionStorage->shouldReceive('create')->andreturn(123);
        $sessionStorage->shouldReceive('getScopes')->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $sessionStorage->shouldReceive('getByAccessToken')->andReturn(
            (new SessionEntity($server))->setId('foobar')
        );
        $sessionStorage->shouldReceive('associateScope');

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');
        $scopeStorage->shouldReceive('get')->andReturn(
            (new ScopeEntity($server))->hydrate(['id' => 'foo'])
        );

        $refreshTokenStorage = M::mock('League\OAuth2\Server\Storage\RefreshTokenInterface');
        $refreshTokenStorage->shouldReceive('setServer');
        $refreshTokenStorage->shouldReceive('create');
        $refreshTokenStorage->shouldReceive('associateScope');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->setAccessTokenStorage($accessTokenStorage);

        $grant->setVerifyCredentialsCallback(function () {
            return false;
        });

        $server->addGrantType($grant, 'sso');
        $response = $server->issueAccessToken();
    }

    public function testCompleteFlow()
    {
        $_POST = [
            'grant_type'   => 'sso',
            'client_id'    =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'     =>  'superadmin@aristamd.com',
            'redirect_uri' =>  '/#/app/referrals',
            'signature'    =>  '3ee3666123455abd48453f62fd181165',
            'scope' => 'foo'
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');
        $sessionStorage->shouldReceive('create')->andreturn(123);
        $sessionStorage->shouldReceive('getScopes')->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $sessionStorage->shouldReceive('getByAccessToken')->andReturn(
            (new SessionEntity($server))->setId('foobar')
        );
        $sessionStorage->shouldReceive('associateScope');

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');
        $scopeStorage->shouldReceive('get')->andReturn(
            (new ScopeEntity($server))->hydrate(['id' => 'foo'])
        );

        $refreshTokenStorage = M::mock('League\OAuth2\Server\Storage\RefreshTokenInterface');
        $refreshTokenStorage->shouldReceive('setServer');
        $refreshTokenStorage->shouldReceive('create');
        $refreshTokenStorage->shouldReceive('associateScope');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->setAccessTokenStorage($accessTokenStorage);

        $grant->setVerifyCredentialsCallback(function () {
            return 123;
        });

        $server->addGrantType($grant, 'sso');
        $response = $server->issueAccessToken();
        $this->assertTrue(array_key_exists('access_token', $response));
        $this->assertTrue(array_key_exists('token_type', $response));
        $this->assertTrue(array_key_exists('expires_in', $response));
    }

    public function testCompleteFlowRefreshToken()
    {
        $_POST = [
            'grant_type'   => 'sso',
            'client_id'    =>  'testapp',
            'client_secret' =>  'foobar',
            'identity'     =>  'superadmin@aristamd.com',
            'redirect_uri' =>  '/#/app/referrals',
            'signature'    =>  '3ee3666123455abd48453f62fd181165',
            'scope' => 'foo'
        ];

        $server = new AuthorizationServer();
        $grant = new SSOGrant();

        $clientStorage = M::mock('League\OAuth2\Server\Storage\ClientInterface');
        $clientStorage->shouldReceive('setServer');
        $clientStorage->shouldReceive('get')->andReturn(
            (new ClientEntity($server))->hydrate(['id' => 'testapp'])
        );

        $sessionStorage = M::mock('League\OAuth2\Server\Storage\SessionInterface');
        $sessionStorage->shouldReceive('setServer');
        $sessionStorage->shouldReceive('create')->andreturn(123);
        $sessionStorage->shouldReceive('getScopes')->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $sessionStorage->shouldReceive('getByAccessToken')->andReturn(
            (new SessionEntity($server))->setId('foobar')
        );
        $sessionStorage->shouldReceive('associateScope');

        $accessTokenStorage = M::mock('League\OAuth2\Server\Storage\AccessTokenInterface');
        $accessTokenStorage->shouldReceive('setServer');
        $accessTokenStorage->shouldReceive('create');
        $accessTokenStorage->shouldReceive('getScopes')->andReturn([
            (new ScopeEntity($server))->hydrate(['id' => 'foo']),
        ]);
        $accessTokenStorage->shouldReceive('associateScope');

        $scopeStorage = M::mock('League\OAuth2\Server\Storage\ScopeInterface');
        $scopeStorage->shouldReceive('setServer');
        $scopeStorage->shouldReceive('get')->andReturn(
            (new ScopeEntity($server))->hydrate(['id' => 'foo'])
        );

        $refreshTokenStorage = M::mock('League\OAuth2\Server\Storage\RefreshTokenInterface');
        $refreshTokenStorage->shouldReceive('setServer');
        $refreshTokenStorage->shouldReceive('create');
        $refreshTokenStorage->shouldReceive('associateScope');

        $server->setClientStorage($clientStorage);
        $server->setScopeStorage($scopeStorage);
        $server->setSessionStorage($sessionStorage);
        $server->setAccessTokenStorage($accessTokenStorage);
        $server->setRefreshTokenStorage($refreshTokenStorage);

        $grant->setVerifyCredentialsCallback(function () {
            return 123;
        });

        $server->addGrantType($grant, 'sso');
        $server->addGrantType(new RefreshTokenGrant());
        $response = $server->issueAccessToken();

        $this->assertTrue(array_key_exists('access_token', $response));
        $this->assertTrue(array_key_exists('refresh_token', $response));
        $this->assertTrue(array_key_exists('token_type', $response));
        $this->assertTrue(array_key_exists('expires_in', $response));
    }
}