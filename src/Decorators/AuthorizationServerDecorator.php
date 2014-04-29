<?php
/**
 * Sugar to work with an authorization server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */
namespace LucaDegasperi\OAuth2Server\Decorators;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Util\RedirectUri;
use League\OAuth2\Server\Exception\ClientException;
use Exception;
use Response;

class AuthorizationServerDecorator extends AuthorizationServer
{
    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $authServer;

    /**
     * Create a new AuthorizationServerDecorator
     *
     * @param \League\OAuth2\Server\AuthorizationServer $authServer the OAuth Authorization Server to use
     */
    public function __construct(AuthorizationServer $authServer)
    {
        $this->authServer = $authServer;
    }

    /**
     * Make a redirect to a client redirect URI
     * @param  string $uri            the uri to redirect to
     * @param  array  $params         the query string parameters
     * @param  string $queryDelimiter the query string delimiter
     * @return string                 a redirect uri
     */
    public function makeRedirect($uri, $params = array(), $queryDelimiter = '?')
    {
        return RedirectUri::make($uri, $params, $queryDelimiter);
    }

    /**
     * Make a redirect with an authorization code
     * 
     * @param  string $code   the authorization code of the redirection
     * @param  array  $params the redirection parameters
     * @return string         a redirect uri
     */
    public function makeRedirectWithCode($code, $params = array())
    {
        return $this->makeRedirect($params['redirect_uri'], array(
            'code'  =>  $code,
            'state' =>  isset($params['state']) ? $params['state'] : '',
        ));
    }

    /**
     * Make a redirect with an error
     * 
     * @param  array   $params the redirection parameters
     * @return string  a redirect uri
     */
    public function makeRedirectWithError($params = array())
    {
        return $this->makeRedirect($params['redirect_uri'], [
            'error' =>  'access_denied',
            'error_message' =>  self::getExceptionMessage('access_denied'),
            'state' =>  isset($params['state']) ? $params['state'] : ''
        ]);
    }

    /**
     * Check the authorization code request parameters
     * 
     * @throws \OAuth2\Exception\ClientException
     * @return array Authorize request parameters
     */
    public function checkAuthorizeParams()
    {
        return $this->authServer->getGrantType('authorization_code')->checkAuthoriseParams();
    }

    /**
     * Authorize a new client
     * @param  string $ownerType The owner type
     * @param  string $ownerId   The owner id
     * @param  array  $options    Additional options to issue an authorization code
     * @return string             An authorization code
     */
    public function newAuthorizeRequest($ownerType, $ownerId, $options = array())
    {
        return $this->authServer->getGrantType('authorization_code')->newAuthoriseRequest($ownerType, $ownerId, $options);
    }

    /**
     * Perform the access token flow
     * 
     * @return Response the appropriate response object
     */
    public function performAccessTokenFlow()
    {
        try {
            // Tell the auth server to issue an access token
            $response = $this->authServer->issueAccessToken();

        } catch (ClientException $e) {

            // Throw an exception because there was a problem with the client's request
            $response = array(
                'error' =>  self::getExceptionType($e->getCode()),
                'error_description' => $e->getMessage()
            );

            // make this better in order to return the correct headers via the response object
            $error = self::getExceptionType($e->getCode());
            $headers = self::getExceptionHttpHeaders($error);
            return Response::json($response, self::$exceptionHttpStatusCodes[$error], $headers);

        } catch (Exception $e) {

            // Throw an error when a non-library specific exception has been thrown
            $response = array(
                'error' =>  'undefined_error',
                'error_description' => $e->getMessage()
            );

            return Response::json($response, 500);
        }

        return Response::json($response);
    }
}
