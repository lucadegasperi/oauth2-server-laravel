<?php
/**
 * OAuth 2.0 Password grant
 *
 * @package     league/oauth2-server
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace LucaDegasperi\OAuth2Server\Grant;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Event;
use League\OAuth2\Server\Exception;
use League\OAuth2\Server\Util\SecureKey;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Grant\AbstractGrant;

/**
 * Password grant class
 */
class TokenGrant extends AbstractGrant
{
    /**
     * The access token
     *
     * @var \League\OAuth2\Server\Entity\AccessTokenEntity
     */
    protected $accessToken;

    /**
     * The resource server (aka the checker).
     *
     * @var \League\OAuth2\Server\ResourceServer
     */
    protected $checker;

    /**
     * Grant identifier
     *
     * @var string
     */
    protected $identifier = 'password';

    /**
     * Response type
     *
     * @var string
     */
    protected $responseType;

    /**
     * Callback to authenticate a user's name and password
     *
     * @var callable
     */
    protected $callback;

    /**
     * Access token expires in override
     *
     * @var int
     */
    protected $accessTokenTTL;

    /**
     * Set the callback to verify a user's username and password
     *
     * @param callable $callback The callback function
     *
     * @return void
     */
    public function setVerifyCredentialsCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Return the callback function
     *
     * @return callable
     *
     * @throws
     */
    protected function getVerifyCredentialsCallback()
    {
        if (is_null($this->callback) || !is_callable($this->callback)) {
            throw new Exception\ServerErrorException('Null or non-callable callback set on Password grant');
        }

        return $this->callback;
    }

    /**
     * Complete the password grant
     *
     * @return array
     *
     * @throws
     */
    public function completeFlow()
    {
        // Get the required params
        $clientId = $this->server->getRequest()->request->get('client_id', $this->server->getRequest()->getUser());
        if (is_null($clientId)) {
            throw new Exception\InvalidRequestException('client_id');
        }

        $clientSecret = $this->server->getRequest()->request->get('client_secret',
            $this->server->getRequest()->getPassword());
        if (is_null($clientSecret)) {
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

        //var_dump($this->access_token);
        //exit();

        $access_token = $this->server->getRequest()->headers->get('authorization',null);
        if ($access_token === null) {
            throw new Exception\AccessDeniedException();
        }

        //var_dump($this->access_token);
        //exit();

        $bearerToken = str_replace( 'Bearer ','',$access_token );

        //var_dump($this->access_token);
        //exit();

        // Set the access token
        $this->accessToken = $this->server->getAccessTokenStorage()->get($bearerToken);

        // Ensure the access token exists
        if (!$this->accessToken instanceof AccessTokenEntity) {
            throw new Exception\AccessDeniedException();
        }

        //var_dump($this->access_token);
        //exit();

        // Check the access token hasn't expired
        // Ensure the auth code hasn't expired
        if ($this->accessToken->isExpired() === true) {
            throw new Exception\AccessDeniedException();
        }

        // Get the scopes for the original session
        $session = $this->accessToken->getSession();
        $scopes = $this->formatScopes($session->getScopes());

        // Get and validate any requested scopes
        $requestedScopesString = $this->server->getRequest()->request->get('scope', '');
        $requestedScopes = $this->validateScopes($requestedScopesString, $client);

        // Generate a new access token and assign it the correct sessions
        $newAccessToken = new AccessTokenEntity($this->server);
        $newAccessToken->setId(SecureKey::generate());
        $newAccessToken->setExpireTime($this->getAccessTokenTTL() + time());
        $newAccessToken->setSession($session);

        foreach ($scopes as $newScope) {
            $newAccessToken->associateScope($newScope);
        }

        $newAccessToken->save();

        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $newAccessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $this->getAccessTokenTTL());

        return $this->server->getTokenType()->generateResponse();
    }
}
