<?php
/**
 * OAuth 2.0 SSO grant
 *
 * @package     league/oauth2-server
 * @author      Luis Cordero H. (lcordero@wearegap.com)
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace LucaDegasperi\OAuth2Server\Grant;

use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Event;
use League\OAuth2\Server\Exception;
use League\OAuth2\Server\Util\SecureKey;

/**
 * Password grant class
 */
class SSOGrant extends AbstractGrant
{
    /**
    * Define constants for the name of properties expected into the credentials.
    */
    const  SSO_IDENTITY_FIELD = 'identity';
    const SSO_REDIRECT_URI_FIELD = 'redirect_uri';
    const SSO_SIGNATURE_FIELD = 'signature';

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
        if (is_null($this->callback) || !is_callable($this->callback))
        {
            throw new Exception\ServerErrorException('Null or non-callable callback set on SSO grant');
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

        $identity = $this->server->getRequest()->request->get(self::SSO_IDENTITY_FIELD, null);
        if (is_null($identity))
        {
            throw new Exception\InvalidRequestException('identity');
        }

        $redirect_uri = $this->server->getRequest()->request->get(self::SSO_REDIRECT_URI_FIELD, null);
        if ( is_null($redirect_uri) )
        {
            throw new Exception\InvalidRequestException('redirect_uri');
        }

        $signature = $this->server->getRequest()->request->get(self::SSO_SIGNATURE_FIELD, null);
        if ( is_null($signature) )
        {
            throw new Exception\InvalidRequestException('signature');
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

        $credentials = [
            self::SSO_IDENTITY_FIELD => $identity,
            self::SSO_REDIRECT_URI_FIELD => $redirect_uri,
            self::SSO_SIGNATURE_FIELD => $signature
        ];

        // Check if user's username and password are correct
        $userId = call_user_func($this->getVerifyCredentialsCallback(), $credentials );

        if ($userId === false) {
            $this->server->getEventEmitter()->emit(new Event\UserAuthenticationFailedEvent($this->server->getRequest()));
            throw new Exception\InvalidCredentialsException();
        }

        // Validate any scopes that are in the request
        $scopeParam = $this->server->getRequest()->request->get('scope', '');
        $scopes = $this->validateScopes($scopeParam, $client);

        // Create a new session
        $session = new SessionEntity($this->server);
        $session->setOwner('user', $userId);
        $session->associateClient($client);

        // Generate an access token
        $accessToken = new AccessTokenEntity($this->server);
        $accessToken->setId(SecureKey::generate());
        $accessToken->setExpireTime($this->getAccessTokenTTL() + time());

        // Associate scopes with the session and access token
        foreach ($scopes as $scope) {
            $session->associateScope($scope);
        }

        foreach ($session->getScopes() as $scope) {
            $accessToken->associateScope($scope);
        }

        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $accessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $this->getAccessTokenTTL());

        // Associate a refresh token if set
        if ($this->server->hasGrantType('refresh_token')) {
            $refreshToken = new RefreshTokenEntity($this->server);
            $refreshToken->setId(SecureKey::generate());
            $refreshToken->setExpireTime($this->server->getGrantType('refresh_token')->getRefreshTokenTTL() + time());
            $this->server->getTokenType()->setParam('refresh_token', $refreshToken->getId());
        }

        // Save everything
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
