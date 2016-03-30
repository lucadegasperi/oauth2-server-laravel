<?php

namespace LucaDegasperi\OAuth2Server;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard as IlluminateGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Psr\Http\Message\ServerRequestInterface as Request;
use League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Server as ResourceServer;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Guard implements IlluminateGuard
{
    use GuardHelpers;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ClientEntityInterface
     */
    private $client = null;

    /**
     * @var array
     */
    private $scopes = null;

    /**
     * @var string
     */
    private $accessToken = null;

    /**
     * @var ResourceServer
     */
    private $resourceServer;

    /**
     * Guard constructor.
     *
     * @param UserProvider $provider
     * @param ResourceServer $resourceServer
     * @param Request $request
     */
    public function __construct(UserProvider $provider, ResourceServer $resourceServer, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->resourceServer = $resourceServer;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $this->parseRequest();

        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  mixed $user
     * @param  array $credentials
     *
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return !is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }


    public function scopes()
    {
        if (!is_null($this->scopes)) {
            return $this->scopes;
        }

        $this->parseRequest();

        return $this->scopes;
    }

    public function accessToken()
    {
        if (!is_null($this->accessToken)) {
            return $this->accessToken;
        }

        $this->parseRequest();

        return $this->accessToken;
    }


    /**
     * Get the client doing the request
     */
    public function client()
    {
        if (!is_null($this->client)) {
            return $this->client;
        }

        $this->parseRequest();
    }

    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Set the current request instance.
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    protected function parseRequest()
    {
        try {

            $this->request = $this->resourceServer->validateAuthenticatedRequest($this->request);

            $this->user = $this->provider->retrieveById($this->request->getAttribute('oauth_user_id'));

            // TODO: parse client into entity
            $this->client = $this->request->getAttribute('oauth_client_id');
            $this->scopes = $this->request->getAttribute('oauth_scopes', []);

        } catch (OAuthServerException $exception) {
            $this->user = null;
            $this->client = null;
            $this->accessToken = null;
        }
    }

    public function getResourceServer()
    {
        return $this->resourceServer;
    }

    public function setResourceServer(ResourceServer $server)
    {
        $this->resourceServer = $server;
        return $this;
    }
}