<?php
/**
 * Laravel Service Provider for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server;

use League\OAuth2\Server\AuthorizationServer as Issuer;
use League\OAuth2\Server\ResourceServer as Checker;
use LucaDegasperi\OAuth2Server\Delegates\AccessTokenIssuerDelegate;
use LucaDegasperi\OAuth2Server\Delegates\AccessTokenValidatorDelegate;
use LucaDegasperi\OAuth2Server\Delegates\AuthCodeCheckerDelegate;
use League\OAuth2\Server\Exception\OAuthException;
use Symfony\Component\HttpFoundation\Request;

class Authorizer
{
    /**
     * The authorization server (aka the issuer)
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $issuer;

    /**
     * The resource server (aka the checker)
     * @var \League\OAuth2\Server\ResourceServer
     */
    protected $checker;

    /**
     * The auth code request parameters
     * @var array
     */
    protected $authCodeRequestParams;

    /**
     * Create a new Authorizer instance
     * @param Issuer $issuer
     * @param Checker $checker
     */
    public function __construct(Issuer $issuer, Checker $checker)
    {
        $this->issuer = $issuer;
        $this->checker = $checker;
        $this->authCodeRequestParams = [];
    }

    /**
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @return \League\OAuth2\Server\ResourceServer
     */
    public function getChecker()
    {
        return $this->checker;
    }

    /**
     * Issue an access token if the request parameters are valid
     * @param AccessTokenIssuerDelegate $delegate
     * @return mixed|\Illuminate\Http\Response a response object for the protocol in use
     */
    public function issueAccessToken(AccessTokenIssuerDelegate $delegate)
    {
        try {
            $responseMessage = $this->issuer->issueAccessToken();
            return $delegate->accessTokenIssued($responseMessage);
        } catch (OAuthException $e) {
            return $delegate->accessTokenIssuingFailed($e);
        }
    }

    /**
     * Get the Auth Code request parameters
     * @return array
     */
    public function getAuthCodeRequestParams()
    {
        return $this->authCodeRequestParams;
    }

    /**
     * @param AuthCodeCheckerDelegate $delegate
     * @return mixed|\Illuminate\Http\Response a response appropriate for the protocol in use
     */
    public function checkAuthCodeRequest(AuthCodeCheckerDelegate $delegate)
    {
        try {
            $this->authCodeRequestParams = $this->issuer->getGrantType('authorization_code')->checkAuthoriseParams();
            return $delegate->checkSuccessful();
        } catch(OAuthException $e) {
            return $delegate->checkFailed($e);
        }
    }

    /**
     * Issue an auth code
     * @param string $ownerType the auth code owner type
     * @param string $ownerId the auth code owner id
     * @param array $params additional parameters to merge
     * @return string the auth code redirect url
     */
    public function issueAuthCode($ownerType, $ownerId, $params = array())
    {
        $params = array_merge($this->authCodeRequestParams, $params);
        return $this->issuer->getGrantType('authorization_code')->newAuthoriseRequest($ownerType, $ownerId, $params);
    }

    /**
     * Validate a request with an access token in it
     * @param AccessTokenValidatorDelegate $delegate the responsible for returning an appropriate response
     * @param bool $httpHeadersOnly whether or not to check only the http headers of the request
     * @param string|null $accessToken an access token to validate
     * @return mixed
     */
    public function validateAccessToken(AccessTokenValidatorDelegate $delegate, $httpHeadersOnly = false, $accessToken = null)
    {
        if ($this->checker->isValidRequest($httpHeadersOnly, $accessToken)) {
            return $delegate->accessTokenValidated();
        } else {
            return $delegate->accessTokenValidationFailed();
        }
    }

    /**
     * get the scopes associated with the current request
     * @return array
     */
    public function getScopes()
    {
        return $this->checker->getScopes();
    }

    /**
     * Check if the current request has all the scopes passed
     * @param string|array $scope the scope(s) to check for existence
     * @return bool
     */
    public function hasScope($scope)
    {
        return $this->checker->hasScope($scope);
    }

    /**
     * Get the resource owner ID of the current request
     * @return string
     */
    public function getResourceOwnerId()
    {
        return $this->checker->getOwnerId();
    }

    /**
     * Get the resource owner type of the current request (client or user)
     * @return string
     */
    public function getResourceOwnerType()
    {
        return $this->checker->getOwnerType();
    }

    /**
     * get the client id of the current request
     * @return string
     */
    public function getClientId()
    {
        return $this->checker->getClientId();
    }

    /**
     * Set the request to use on the issuer and checker
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->issuer->setRequest($request);
        $this->checker->setRequest($request);
    }
}
