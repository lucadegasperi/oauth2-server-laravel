<?php

use LucaDegasperi\OAuth2Server\Repositories\FluentScope;

class FluentScopeTest extends TestCase
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

    public function test_get_unexisting_scope()
    {
        $repo = new FluentScope();
        $repo->limitClientsToScopes(true);
        $repo->limitScopesToGrants(true);
        $result = $repo->getScope('scope3', 'client3', 'grant3');

        $this->assertTrue($repo->areClientsLimitedToScopes());
        $this->assertTrue($repo->areScopesLimitedToGrants());

        $this->assertFalse($result);
    }

    public function test_get_scope_with_client_and_grant()
    {
        $repo = new FluentScope();
        $repo->limitClientsToScopes(true);
        $repo->limitScopesToGrants(true);
        $result = $repo->getScope('scope1', 'client1id', 'grant1');  

        $this->resultAssertions($result); 
    }

    public function test_get_scope_with_client_only()
    {
        $repo = new FluentScope();
        $repo->limitClientsToScopes(true);
        $result = $repo->getScope('scope1', 'client1id');

        $this->resultAssertions($result);    
    }

    public function test_get_scope_with_grant_only()
    {
        $repo = new FluentScope();
        $repo->limitScopesToGrants(true);
        $result = $repo->getScope('scope1', 'whatever', 'grant1'); 

        $this->resultAssertions($result);   
    }

    public function test_get_scope()
    {
        $repo = new FluentScope();
        $result = $repo->getScope('scope1');  

        $this->resultAssertions($result);  
    }

    public function resultAssertions($result)
    {
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('scope', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('scope1', $result['scope']);
        $this->assertEquals('scope1', $result['name']);
        $this->assertEquals('Scope 1 Description', $result['description']);
    }
}