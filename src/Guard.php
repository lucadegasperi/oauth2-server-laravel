<?php
/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard as IlluminateGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

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
     * @var OAuthServerException
     */
    private $exception = null;

    /**
     * @var ResourceServer
     */
    private $resourceServer;
    /**
     * @var ClientRepositoryInterface
     */
    private $clientRepository;

    /**
     * Guard constructor.
     *
     * @param UserProvider $provider
     * @param ResourceServer $resourceServer
     * @param Request $request
     * @param ClientRepositoryInterface $clientRepository
     */
    public function __construct(
        UserProvider $provider,
        ResourceServer $resourceServer,
        Request $request,
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->provider = $provider;
        $this->resourceServer = $resourceServer;
        $psr7Factory = new DiactorosFactory();
        $this->request = $psr7Factory->createRequest($request);
        $this->clientRepository = $clientRepository;
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

        return $this->client;
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
        $psr7Factory = new DiactorosFactory();
        $this->request = $psr7Factory->createRequest($request);
        return $this;
    }

    protected function parseRequest()
    {
        try {

            $this->request = $this->resourceServer->validateAuthenticatedRequest($this->request);

            $this->user = $this->provider->retrieveById($this->request->getAttribute('oauth_user_id'));
            $this->client = $this->clientRepository->getClientEntity($this->request->getAttribute('oauth_client_id'));
            $this->scopes = $this->request->getAttribute('oauth_scopes', []);
            $this->accessToken = $this->request->getAttribute('oauth_access_token_id');

        } catch (OAuthServerException $exception) {
            $this->user = null;
            $this->client = null;
            $this->accessToken = null;
            $this->exception = $exception;
        }
        // TODO: catch other exceptions as well.
    }

    public function getException()
    {
        return $this->exception;
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