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
 * 
 * This class creates a new Refresh token and asign it to the user session. Per
 * session we are going to have only one refresh token and with it you can
 * create as many access tokens as required. Every time that you access the Api
 * the expiration time of the refresh token will be delay by 45 minutes(depending on configuration)
 * The expiration fo the Access Token is always the same and can't be extended,
 * once it experices you will need to create a new one using the refresh token.
 * If the refresh token has expired then you won't be able to create a new access
 * token and because of that you will be redirected to the login page.
 */
class RefreshTokenGrant extends RefreshTokenGrantOriginal
{
    /**
     * {@inheritdoc}
     */
    public function completeFlow()
    {
        // Get clientid from request
        $clientId = $this->server->getRequest()->request->get('client_id', $this->server->getRequest()->getUser());
        if (is_null($clientId)) {
            throw new Exception\InvalidRequestException('client_id');
        }

        // Get client secret from request
        $clientSecret = $this->server->getRequest()->request->get('client_secret',
            $this->server->getRequest()->getPassword());
        if ($this->shouldRequireClientSecret() && is_null($clientSecret)) {
            throw new Exception\InvalidRequestException('client_secret');
        }

        // Validate client ID and client secret and return the client.
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

        // Gets the active refresh token for the current session.
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
        
        // Using the expiration time of the current refresh token calculates the 
        // reminding time for the new access token.
        $expireTime = $oldRefreshToken->getExpireTime();
        $remindingTime = $expireTime - time();

        // Ensure the old refresh token hasn't expired
        if ($oldRefreshToken->isExpired() === true) {
            throw new Exception\InvalidRefreshException();
        }

        // It gets the previous access token.
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

        // Generate a new access token and assign it to the right sessions
        $newAccessToken = new AccessTokenEntity($this->server);
        $newAccessToken->setId(SecureKey::generate());
        $newAccessToken->setExpireTime($expireTime);
        $newAccessToken->setSession($session);

        foreach ($newScopes as $newScope) {
            $newAccessToken->associateScope($newScope);
        }

        // Save the new access token that is going to be returned to the user.
        $newAccessToken->save();

        // We need to assign the new access token to the current user session.
        // The exipiration time should be reminding time on the current refresh token
        // in that way event if you get a new access token a 10 seconds before the 
        // refresh token expires the new access token will have only 10 seconds left.
        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $newAccessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $remindingTime);

        // Now we need to change the refresh token before exipiring the old access token,
        // otherwise it will delete the refresh token because of the delete cascade constraint 
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
            // Here you will change the refresh token and removes the reference to the old access token
            // and set the reference to the new access token.
            $oldRefreshToken->setAccessToken($newAccessToken);
            $oldRefreshToken->save();
            $this->server->getTokenType()->setParam('refresh_token', $oldRefreshToken->getId());
        }

        // Now its save to expire the old token, in that way it won't be available anymore.
        $oldAccessToken->expire();
        
        // Return a response with the tokens details
        return $this->server->getTokenType()->generateResponse();
    }
}