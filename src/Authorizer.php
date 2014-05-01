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
use League\OAuth2\Server\Exception\ClientException;
use League\OAuth2\Server\ResourceServer as Checker;
use Exception;
use LucaDegasperi\OAuth2Server\Delegates\AccessTokenIssuerDelegate;
use LucaDegasperi\OAuth2Server\Delegates\AccessTokenValidatorDelegate;
use LucaDegasperi\OAuth2Server\Delegates\AuthCodeCheckerDelegate;
use League\OAuth2\Server\Exception\OAuthException;

class Authorizer {

    protected $issuer;

    protected $checker;

    protected $authCodeRequestParams;

    public function __construct(Issuer $issuer, Checker $checker)
    {
        $this->issuer = $issuer;
        $this->checker = $checker;
        $this->authCodeRequestParams = [];
    }

    public function issueAccessToken(AccessTokenIssuerDelegate $delegate)
    {
        try {
            $responseMessage = $this->issuer->issueAccessToken();
            return $delegate->accessTokenIssued($responseMessage);
        } catch (OAuthException $e) {
            return $delegate->accessTokenIssuingFailed($e);
        }
    }

    public function getAuthCodeRequestParams()
    {
        return $this->authCodeRequestParams;
    }

    public function checkAuthCodeRequest(AuthCodeCheckerDelegate $delegate)
    {
        try {
            $this->authCodeRequestParams = $this->issuer->getGrantType('authorization_code')->checkAuthoriseParams();
            return $delegate->checkSuccessful();
        } catch(ClientException $e) {
            return $delegate->checkFailed($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            return $delegate->checkFailed(5, $e->getMessage());
        }
    }

    public function issueAuthCode($ownerType, $ownerId, $params = array())
    {
        $params = array_merge($this->authCodeRequestParams, $params);
        return $this->issuer->getGrantType('authorization_code')->newAuthoriseRequest($ownerType, $ownerId, $params);
    }

    public function validateAccessToken(AccessTokenValidatorDelegate $delegate, $httpHeadersOnly = false, $accessToken = null)
    {
        if ($this->checker->isValidRequest($httpHeadersOnly, $accessToken)) {
            return $delegate->accessTokenValidated();
        } else {
            return $delegate->accessTokenValidationFailed();
        }
    }

    public function getScopes()
    {
        $this->checker->getScopes();
    }

    public function hasScope($scope)
    {
        return $this->checker->hasScope($scope);
    }

    public function getResourceOwnerId()
    {
        return $this->checker->getOwnerId();
    }

    public function getResourceOwnerType()
    {
        return $this->checker->getOwnerType();
    }

    public function getClientId()
    {
        return $this->checker->getClientId();
    }
} 