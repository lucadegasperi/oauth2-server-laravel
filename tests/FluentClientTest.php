<?php

use LucaDegasperi\OAuth2Server\Repositories\FluentClient;

class FluentClientTest extends DBTestCase
{

    public function test_get_client_with_secret_only()
    {
        // arrange
        $repo = new FluentClient();

        // act
        $client = $repo->getClient('client1id', 'client1secret');

        // assert
        $this->assertIsClient($client, false);
    }

    public function test_get_client_with_redirect_uri_only()
    {
        $repo = new FluentClient();
        $client = $repo->getClient('client1id', null, 'http://example1.com/callback');

        $this->assertIsClient($client);
    }

    public function test_get_client_with_secret_and_redirect_uri()
    {
        $repo = new FluentClient();

        $client = $repo->getClient('client1id', 'client1secret', 'http://example1.com/callback');

        $this->assertIsClient($client);
    }

    public function test_null_is_returned_with_unexisting_client()
    {
        $repo = new FluentClient();

        $result1 = $repo->getClient("client3id", "client3secret");
        $result2 = $repo->getClient('client3id', null, 'http://example3.com/callback');
        $result3 = $repo->getClient('client3id', 'client3secret', 'http://example3.com/callback');

        $this->assertNull($result1);
        $this->assertNull($result2);
        $this->assertNull($result3);
    }

    public function test_false_is_returned_with_invalid_grant()
    {
        $repo = new FluentClient();
        $repo->limitClientsToGrants(true);

        $result = $repo->getClient('client1id', 'client1secret', 'http://example1.com/callback', 'grant2');

        $this->assertTrue($repo->areClientsLimitedToGrants());
        $this->assertNull($result);
    }

    public function test_client_is_returned_with_valid_grant()
    {
        $repo = new FluentClient();
        $repo->limitClientsToGrants(true);

        $client = $repo->getClient('client1id', 'client1secret', 'http://example1.com/callback', 'grant1');

        $this->assertTrue($repo->areClientsLimitedToGrants());
        $this->assertIsClient($client);
    }

    public function assertIsClient($client, $redirectUri = true)
    {
        $this->assertInstanceOf('League\OAuth2\Server\Entity\Client', $client);
        $this->assertEquals('client1id', $client->getId());
        $this->assertEquals('client1secret', $client->getSecret());
        $this->assertEquals('client1', $client->getName());
        if ($redirectUri) {
            $this->assertEquals('http://example1.com/callback', $client->getRedirectUri());
        } else {
            $this->assertNull($client->getRedirectUri());
        }
    }
}
