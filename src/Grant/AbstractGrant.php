<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Grant;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Event\ClientAuthenticationFailedEvent;
use League\OAuth2\Server\Event\UserAuthenticationFailedEvent;
use League\OAuth2\Server\Exception\InvalidClientException;
use League\OAuth2\Server\Exception\InvalidCredentialsException;
use League\OAuth2\Server\Exception\InvalidRequestException;
use League\OAuth2\Server\Grant\AbstractGrant as Grant;
use League\OAuth2\Server\Util\SecureKey;

/**
 * This is the abstract grant class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
abstract class AbstractGrant extends Grant
{
    /**
     * Response type.
     *
     * @var string
     */
    protected $responseType;

    /**
     * Access token expires in override.
     *
     * @var int
     */
    protected $accessTokenTTL;

    /**
     * Validate the data and check the credentials.
     *
     * @throws \League\OAuth2\Server\Exception\InvalidRequestException
     *
     * @return void
     */
    abstract protected function authenticate();

    /**
     * Complete the password grant.
     *
     * @throws
     *
     * @return array
     */
    public function completeFlow()
    {
        // Get the required params.
        $clientId = $this->server->getRequest()->request->get('client_id', $this->server->getRequest()->getUser());
        if (is_null($clientId)) {
            throw new InvalidRequestException('client_id');
        }

        $clientSecret = $this->server->getRequest()->request->get('client_secret',
            $this->server->getRequest()->getPassword());
        if (is_null($clientSecret)) {
            throw new InvalidRequestException('client_secret');
        }

        // Validate client ID and client secret.
        $client = $this->server->getClientStorage()->get(
            $clientId,
            $clientSecret,
            null,
            $this->getIdentifier()
        );

        if (!($client instanceof ClientEntity)) {
            $this->server->getEventEmitter()->emit(new ClientAuthenticationFailedEvent($this->server->getRequest()));
            throw new InvalidClientException();
        }

        // Authenticate and return the user.
        $userId = $this->authenticate();

        if (!$userId) {
            $this->server->getEventEmitter()->emit(new UserAuthenticationFailedEvent($this->server->getRequest()));

            throw new InvalidCredentialsException();
        }

        // Validate any scopes that are in the request.
        $scopeParam = $this->server->getRequest()->request->get('scope', '');
        $scopes = $this->validateScopes($scopeParam, $client);

        // Create a new session.
        $session = new SessionEntity($this->server);
        $session->setOwner('user', $userId);
        $session->associateClient($client);

        // Generate an access token.
        $accessToken = new AccessTokenEntity($this->server);
        $accessToken->setId(SecureKey::generate());
        $accessToken->setExpireTime($this->getAccessTokenTTL() + time());

        // Associate scopes with the session and access token.
        foreach ($scopes as $scope) {
            $session->associateScope($scope);
        }

        foreach ($session->getScopes() as $scope) {
            $accessToken->associateScope($scope);
        }

        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $accessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $this->getAccessTokenTTL());

        // Associate a refresh token if set.
        if ($this->server->hasGrantType('refresh_token')) {
            $refreshToken = new RefreshTokenEntity($this->server);
            $refreshToken->setId(SecureKey::generate());
            $refreshToken->setExpireTime($this->server->getGrantType('refresh_token')->getRefreshTokenTTL() + time());
            $this->server->getTokenType()->setParam('refresh_token', $refreshToken->getId());
        }

        // Save everything.
        $session->save();
        $accessToken->setSession($session);
        $accessToken->save();

        if ($this->server->hasGrantType('refresh_token')) {
            $refreshToken->setAccessToken($accessToken);
            $refreshToken->save();
        }

        return $this->server->getTokenType()->generateResponse();
    }
}
