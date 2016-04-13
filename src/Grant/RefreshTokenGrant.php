<?php
/**
 * OAuth 2.0 Refresh token grant
 *
 * @package     league/oauth2-server
 * @author      Luis Diego Cordero Hernández <lcordero@wearegap.com>
 * @copyright   Copyright (c) Aristamd.com
 * @license     http://mit-license.org/
 * @link        https://github.com/aristamd/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Grant;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Event;
use League\OAuth2\Server\Exception;
use League\OAuth2\Server\Util\SecureKey;
use League\OAuth2\Server\Grant\RefreshTokenGrant as RefreshTokenGrantOriginal;

/**
 * Refresh token grant
 */
class RefreshTokenGrant extends RefreshTokenGrantOriginal
{
    /**
     * {@inheritdoc}
     */
    public function completeFlow()
    {
        $clientId = $this->server->getRequest()->request->get('client_id', $this->server->getRequest()->getUser());
        if (is_null($clientId)) {
            throw new Exception\InvalidRequestException('client_id');
        }

        $clientSecret = $this->server->getRequest()->request->get('client_secret',
            $this->server->getRequest()->getPassword());
        if ($this->shouldRequireClientSecret() && is_null($clientSecret)) {
            throw new Exception\InvalidRequestException('client_secret');
        }

        // Validate client ID and client secret
        $client = $this->server->getClientStorage()->get(
            $clientId,
            $clientSecret,
            null,
            $this->getIdentifier()
        );

        if (($client instanceof ClientEntity) === false) {
            $this->server->getEventEmitter()->emit(new Event\ClientAuthenticationFailedEvent($this->server->getRequest()));
            throw new Exception\InvalidClientException();
        }

        $oldRefreshTokenParam = $this->server->getRequest()->request->get('refresh_token', null);
        if ($oldRefreshTokenParam === null) {
            throw new Exception\InvalidRequestException('refresh_token');
        }

        // Validate refresh token
        $oldRefreshToken = $this->server->getRefreshTokenStorage()->get($oldRefreshTokenParam);

        if (($oldRefreshToken instanceof RefreshTokenEntity) === false) {
            throw new Exception\InvalidRefreshException();
        }
        
        if ($oldRefreshToken->getExpireTime() < time()) {
            throw new Exception\InvalidRefreshException();
        }
        
        $expireTime = $oldRefreshToken->getExpireTime();
        $remindingTime = $expireTime - time();

        // Ensure the old refresh token hasn't expired
        if ($oldRefreshToken->isExpired() === true) {
            throw new Exception\InvalidRefreshException();
        }

        $oldAccessToken = $oldRefreshToken->getAccessToken();

        // Get the scopes for the original session
        $session = $oldAccessToken->getSession();
        $scopes = $this->formatScopes($session->getScopes());

        // Get and validate any requested scopes
        $requestedScopesString = $this->server->getRequest()->request->get('scope', '');
        $requestedScopes = $this->validateScopes($requestedScopesString, $client);

        // If no new scopes are requested then give the access token the original session scopes
        if (count($requestedScopes) === 0) {
            $newScopes = $scopes;
        } else {
            // The OAuth spec says that a refreshed access token can have the original scopes or fewer so ensure
            //  the request doesn't include any new scopes
            foreach ($requestedScopes as $requestedScope) {
                if (!isset($scopes[$requestedScope->getId()])) {
                    throw new Exception\InvalidScopeException($requestedScope->getId());
                }
            }

            $newScopes = $requestedScopes;
        }

        // Generate a new access token and assign it the correct sessions
        $newAccessToken = new AccessTokenEntity($this->server);
        $newAccessToken->setId(SecureKey::generate());
        $newAccessToken->setExpireTime($expireTime);
        $newAccessToken->setSession($session);

        foreach ($newScopes as $newScope) {
            $newAccessToken->associateScope($newScope);
        }

        // Expire the old token and save the new one
        $oldAccessToken->expire();
        $newAccessToken->save();

        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $newAccessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $remindingTime);

        if ($this->shouldRotateRefreshTokens()) {
            // Expire the old refresh token
            $oldRefreshToken->expire();

            // Generate a new refresh token
            $newRefreshToken = new RefreshTokenEntity($this->server);
            $newRefreshToken->setId(SecureKey::generate());
            $newRefreshToken->setExpireTime($expireTime);
            $newRefreshToken->setAccessToken($newAccessToken);
            $newRefreshToken->save();

            $this->server->getTokenType()->setParam('refresh_token', $newRefreshToken->getId());
        } else {
            $oldRefreshToken->setAccessToken($newAccessToken);
            $oldRefreshToken->save();
            $this->server->getTokenType()->setParam('refresh_token', $oldRefreshToken->getId());
        }

        return $this->server->getTokenType()->generateResponse();
    }
}