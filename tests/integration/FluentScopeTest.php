<?php

use LucaDegasperi\OAuth2Server\Storage\FluentScope;
use Mockery as m;

class FluentScopeTest extends DBTestCase
{
    public function getScopeRepository()
    {
        $server = m::mock('League\OAuth2\Server\AbstractServer');
        $repo = new FluentScope($this->app['db']);
        $repo->setServer($server);

        return $repo;
    }

    public function test_get_unexisting_scope()
    {
        $repo = $this->getScopeRepository();
        $repo->limitClientsToScopes(true);
        $repo->limitScopesToGrants(true);

        $result = $repo->get('scope3', 'grant3', 'client3id');

        $this->assertTrue($repo->areClientsLimitedToScopes());
        $this->assertTrue($repo->areScopesLimitedToGrants());
        $this->assertNull($result);
    }

    public function test_get_scope_with_client_only()
    {
        $repo = $this->getScopeRepository();
        $repo->limitClientsToScopes(true);

        $result = $repo->get('scope1', null, 'client1id');

        $this->assertIsScope($result);
    }

    public function test_get_scope_with_invalid_client_only()
    {
        $repo = $this->getScopeRepository();
        $repo->limitClientsToScopes(true);

        $result = $repo->get('scope1', null, 'invalidclientid');

        $this->assertTrue($repo->areClientsLimitedToScopes());
        $this->assertNull($result);
    }

    public function test_get_scope_with_grant_only()
    {
        $repo = $this->getScopeRepository();
        $repo->limitScopesToGrants(true);

        $result = $repo->get('scope1', 'grant1');

        $this->assertIsScope($result);
    }

    public function test_get_scope_with_invalid_grant_only()
    {
        $repo = $this->getScopeRepository();
        $repo->limitScopesToGrants(true);

        $result = $repo->get('scope1', 'invalidgrant');

        $this->assertTrue($repo->areScopesLimitedToGrants());
        $this->assertNull($result);
    }

    public function test_get_scope_with_client_and_grant()
    {
        $repo = $this->getScopeRepository();
        $repo->limitClientsToScopes(true);
        $repo->limitScopesToGrants(true);

        $result = $repo->get('scope1', 'grant1', 'client1id');

        $this->assertTrue($repo->areClientsLimitedToScopes());
        $this->assertTrue($repo->areScopesLimitedToGrants());
        $this->assertIsScope($result);
    }

    public function test_get_scope()
    {
        $repo = $this->getScopeRepository();
        $result = $repo->get('scope1');  

        $this->assertIsScope($result);
    }

    public function assertIsScope($result)
    {
        $this->assertInstanceOf('League\OAuth2\Server\Entity\ScopeEntity', $result);
        $this->assertEquals('scope1', $result->getId());
        $this->assertEquals('Scope 1 Description', $result->getDescription());
    }
}
