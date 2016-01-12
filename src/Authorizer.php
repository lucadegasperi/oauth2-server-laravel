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

use League\OAuth2\Server\AuthorizationServer as Issuer;
use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\ResourceServer as Checker;
use League\OAuth2\Server\TokenType\TokenTypeInterface;
use League\OAuth2\Server\Util\RedirectUri;
use LucaDegasperi\OAuth2Server\Exceptions\NoActiveAccessTokenException;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is the authorizer class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class Authorizer
{
    /**
     * The authorization server (aka the issuer).
     *
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $issuer;

    /**
     * The resource server (aka the checker).
     *
     * @var \League\OAuth2\Server\ResourceServer
     */
    protected $checker;

    /**
     * The auth code request parameters.
     *
     * @var array
     */
    protected $authCodeRequestParams;

    /**
     * The redirect uri generator.
     *
     * @var bool|null
     */
    protected $redirectUriGenerator = null;

    /**
     * Create a new Authorizer instance.
     *
     * @param \League\OAuth2\Server\AuthorizationServer $issuer
     * @param \League\OAuth2\Server\ResourceServer $checker
     */
    public function __construct(Issuer $issuer, Checker $checker)
    {
        $this->issuer = $issuer;
        $this->checker = $checker;
        $this->authCodeRequestParams = [];
    }

    /**
     * Get the issuer.
     *
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * Get the checker.
     *
     * @return \League\OAuth2\Server\ResourceServer
     */
    public function getChecker()
    {
        return $this->checker;
    }

    /**
     * Get the current access token for the session.
     *
     * If the session does not have an active access token, an exception will be thrown.
     *
     * @throws \LucaDegasperi\OAuth2Server\Exceptions\NoActiveAccessTokenException
     *
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity
     */
    public function getAccessToken()
    {
        $accessToken = $this->getChecker()->getAccessToken();

        if (is_null($accessToken)) {
            throw new NoActiveAccessTokenException('Tried to access session data without an active access token');
        }

        return $accessToken;
    }

    /**
     * Issue an access token if the request parameters are valid.
     *
     * @return array a response object for the protocol in use
     */
    public function issueAccessToken()
    {
        return $this->issuer->issueAccessToken();
    }

    /**
     * Get the Auth Code request parameters.
     *
     * @return array
     */
    public function getAuthCodeRequestParams()
    {
        return $this->authCodeRequestParams;
    }

    /**
     * Get a single parameter from the auth code request parameters.
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public function getAuthCodeRequestParam($key, $default = null)
    {
        if (array_key_exists($key, $this->authCodeRequestParams)) {
            return $this->authCodeRequestParams[$key];
        }

        return $default;
    }

    /**
     * Check the validity of the auth code request.
     *
     * @return null a response appropriate for the protocol in use
     */
    public function checkAuthCodeRequest()
    {
        $this->authCodeRequestParams = $this->issuer->getGrantType('authorization_code')->checkAuthorizeParams();
    }

    /**
     * Issue an auth code.
     *
     * @param string $ownerType the auth code owner type
     * @param string $ownerId the auth code owner id
     * @param array $params additional parameters to merge
     *
     * @return string the auth code redirect url
     */
    public function issueAuthCode($ownerType, $ownerId, $params = [])
    {
        $params = array_merge($this->authCodeRequestParams, $params);

        return $this->issuer->getGrantType('authorization_code')->newAuthorizeRequest($ownerType, $ownerId, $params);
    }

    /**
     * Generate a redirect uri when the auth code request is denied by the user.
     *
     * @return string a correctly formed url to redirect back to
     */
    public function authCodeRequestDeniedRedirectUri()
    {
        $error = new AccessDeniedException();

        return $this->getRedirectUriGenerator()->make($this->getAuthCodeRequestParam('redirect_uri'), [
                        'error' => $error->errorType,
                        'error_description' => $error->getMessage(),
                ]
        );
    }

    /**
     * Get the RedirectUri generator instance.
     *
     * @return RedirectUri
     */
    public function getRedirectUriGenerator()
    {
        if (is_null($this->redirectUriGenerator)) {
            $this->redirectUriGenerator = new RedirectUri();
        }

        return $this->redirectUriGenerator;
    }

    /**
     * Set the RedirectUri generator instance.
     *
     * @param $redirectUri
     */
    public function setRedirectUriGenerator($redirectUri)
    {
        $this->redirectUriGenerator = $redirectUri;
    }

    /**
     * Validate a request with an access token in it.
     *
     * @param bool $httpHeadersOnly whether or not to check only the http headers of the request
     * @param string|null $accessToken an access token to validate
     *
     * @return mixed
     */
    public function validateAccessToken($httpHeadersOnly = false, $accessToken = null)
    {
        return $this->checker->isValidRequest($httpHeadersOnly, $accessToken);
    }

    /**
     * get the scopes associated with the current request.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->getAccessToken()->getScopes();
    }

    /**
     * Check if the current request has all the scopes passed.
     *
     * @param string|array $scope the scope(s) to check for existence
     *
     * @return bool
     */
    public function hasScope($scope)
    {
        if (is_array($scope)) {
            foreach ($scope as $s) {
                if ($this->hasScope($s) === false) {
                    return false;
                }
            }

            return true;
        }

        return $this->getAccessToken()->hasScope($scope);
    }

    /**
     * Get the resource owner ID of the current request.
     *
     * @return string
     */
    public function getResourceOwnerId()
    {
        return $this->getAccessToken()->getSession()->getOwnerId();
    }

    /**
     * Get the resource owner type of the current request (client or user).
     *
     * @return string
     */
    public function getResourceOwnerType()
    {
        return $this->getAccessToken()->getSession()->getOwnerType();
    }

    /**
     * Get the client id of the current request.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->checker->getAccessToken()->getSession()->getClient()->getId();
    }

    /**
     * Set the request to use on the issuer and checker.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->issuer->setRequest($request);
        $this->checker->setRequest($request);
    }

    /**
     * Set the token type to use.
     *
     * @param \League\OAuth2\Server\TokenType\TokenTypeInterface $tokenType
     */
    public function setTokenType(TokenTypeInterface $tokenType)
    {
        $this->issuer->setTokenType($tokenType);
        $this->checker->setTokenType($tokenType);
    }
}
