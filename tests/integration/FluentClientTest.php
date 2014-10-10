<?php

use LucaDegasperi\OAuth2Server\Storage\FluentClient;
use Mockery as m;

class FluentClientTest extends DBTestCase
{
    public function getClientRepository()
    {
        $server = m::mock('League\OAuth2\Server\AbstractServer');
        $repo = new FluentClient($this->app['db']);
        $repo->setServer($server);

        return $repo;
    }

    public function test_get_client_with_secret_only()
    {
        // arrange
        $repo = $this->getClientRepository();

        // act
        $client = $repo->get('client1id', 'client1secret');

        // assert
        $this->assertIsClient($client, false);
    }

    public function test_get_client_with_redirect_uri_only()
    {
        $repo = $this->getClientRepository();
        $client = $repo->get('client1id', null, 'http://example1.com/callback');

        $this->assertIsClient($client);
    }

    public function test_get_client_with_secret_and_redirect_uri()
    {
        $repo = $this->getClientRepository();

        $client = $repo->get('client1id', 'client1secret', 'http://example1.com/callback');

        $this->assertIsClient($client);
    }

    public function test_null_is_returned_with_unexisting_client()
    {
        $repo = $this->getClientRepository();

        $result1 = $repo->get("client3id", "client3secret");
        $result2 = $repo->get('client3id', null, 'http://example3.com/callback');
        $result3 = $repo->get('client3id', 'client3secret', 'http://example3.com/callback');

        $this->assertNull($result1);
        $this->assertNull($result2);
        $this->assertNull($result3);
    }

    public function test_false_is_returned_with_invalid_grant()
    {
        $repo = $this->getClientRepository();
        $repo->limitClientsToGrants(true);

        $result = $repo->get('client1id', 'client1secret', 'http://example1.com/callback', 'grant2');

        $this->assertTrue($repo->areClientsLimitedToGrants());
        $this->assertNull($result);
    }

    public function test_client_is_returned_with_valid_grant()
    {
        $repo = $this->getClientRepository();
        $repo->limitClientsToGrants(true);

        $client = $repo->get('client1id', 'client1secret', 'http://example1.com/callback', 'grant1');

        $this->assertTrue($repo->areClientsLimitedToGrants());
        $this->assertIsClient($client);
    }

    public function test_it_returns_a_client_associated_with_a_valid_session()
    {
        $repo = $this->getClientRepository();

        $session = m::mock('League\OAuth2\Server\Entity\SessionEntity');
        $session->shouldReceive('getId')->once()->andReturn(1);

        $result = $repo->getBySession($session);
        $this->assertIsClient($result, false);
    }

    public function test_it_returns_null_with_an_invalid_session()
    {
        $repo = $this->getClientRepository();

        $session = m::mock('League\OAuth2\Server\Entity\SessionEntity');
        $session->shouldReceive('getId')->once()->andReturn(20);

        $result = $repo->getBySession($session);
        $this->assertNull($result);
    }

    public function assertIsClient($client, $redirectUri = true)
    {
        $this->assertInstanceOf('League\OAuth2\Server\Entity\ClientEntity', $client);
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
