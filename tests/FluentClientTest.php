<?php

use LucaDegasperi\OAuth2Server\Repositories\FluentClient;

class FluentClientTest extends TestCase
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

    public function test_get_client_with_secret_only()
    {
        $repo = new FluentClient();
        $result = $repo->getClient("client1id", "client1secret");

        $this->assertArrayHasKey('client_id', $result);
        $this->assertArrayHasKey('client_secret', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('redirect_uri', $result);
        $this->assertArrayHasKey('metadata', $result);

        $this->assertEquals('client1id', $result['client_id']);
        $this->assertEquals('client1secret', $result['client_secret']);
        $this->assertEquals('client1', $result['name']);
        $this->assertNull($result['redirect_uri']);
        $this->assertTrue(is_array($result['metadata']));
    }

    public function test_get_client_with_redirect_uri_only()
    {
        $repo = new FluentClient();
        $result = $repo->getClient('client1id', null, 'http://example1.com/callback');

        $this->assertArrayHasKey('client_id', $result);
        $this->assertArrayHasKey('client_secret', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('redirect_uri', $result);
        $this->assertArrayHasKey('metadata', $result);

        $this->assertEquals('client1id', $result['client_id']);
        $this->assertEquals('client1secret', $result['client_secret']);
        $this->assertEquals('client1', $result['name']);
        $this->assertEquals('http://example1.com/callback', $result['redirect_uri']);
        $this->assertTrue(is_array($result['metadata']));
    }

    public function test_get_client_with_secret_and_redirect_uri()
    {
        $repo = new FluentClient();
        $result = $repo->getClient('client1id', 'client1secret', 'http://example1.com/callback');

        $this->assertArrayHasKey('client_id', $result);
        $this->assertArrayHasKey('client_secret', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('redirect_uri', $result);
        $this->assertArrayHasKey('metadata', $result);

        $this->assertEquals('client1id', $result['client_id']);
        $this->assertEquals('client1secret', $result['client_secret']);
        $this->assertEquals('client1', $result['name']);
        $this->assertEquals('http://example1.com/callback', $result['redirect_uri']);
        $this->assertTrue(is_array($result['metadata']));
    }

    public function test_false_is_returned_with_unexisting_client()
    {
        $repo = new FluentClient();
        $result1 = $repo->getClient("client3id", "client3secret");
        $result2 = $repo->getClient('client3id', null, 'http://example3.com/callback');
        $result3 = $repo->getClient('client3id', 'client3secret', 'http://example3.com/callback');

        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
    }

    public function test_false_is_returned_with_invalid_grant()
    {
        $repo = new FluentClient();
        $repo->limitClientsToGrants(true);
        $result = $repo->getClient('client1id', 'client1secret', 'http://example1.com/callback', 'grant2');

        $this->assertTrue($repo->areClientsLimitedToGrants());
        $this->assertFalse($result);
    }

    public function test_client_is_returned_with_valid_grant()
    {
        $repo = new FluentClient();
        $repo->limitClientsToGrants(true);
        $result = $repo->getClient('client1id', 'client1secret', 'http://example1.com/callback', 'grant1');

        $this->assertTrue($repo->areClientsLimitedToGrants());
        $this->assertTrue(is_array($result));

        $this->assertArrayHasKey('client_id', $result);
        $this->assertArrayHasKey('client_secret', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('redirect_uri', $result);
        $this->assertArrayHasKey('metadata', $result);

        $this->assertEquals('client1id', $result['client_id']);
        $this->assertEquals('client1secret', $result['client_secret']);
        $this->assertEquals('client1', $result['name']);
        $this->assertEquals('http://example1.com/callback', $result['redirect_uri']);
        $this->assertTrue(is_array($result['metadata']));
    }
}