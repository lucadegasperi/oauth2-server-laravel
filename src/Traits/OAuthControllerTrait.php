<?php

/*
 * This file is part of Laravel OAuth 2.0.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Traits;

use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * This is the oauth controller trait.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
trait OAuthControllerTrait
{
    public function postAccessToken(ServerRequestInterface $request, AuthorizationServer $server)
    {
        $response = new Response();

        try {
            return $server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (\Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))->generateHttpResponse($response);
        }
    }

    public function doAuthorize(ServerRequestInterface $request, AuthorizationServer $server)
    {
        $response = new Response();
        try {
            // Validate the HTTP request and return an AuthorizationRequest object.
            // The auth request object can be serialized into a user's session
            $authRequest = $server->validateAuthorizationRequest($request);
            // Once the user has logged in set the user on the AuthorizationRequest
            if (strtolower($request->getMethod()) === 'post') {
                $authRequest->setUser(Auth::user());

                // (true = approved, false = denied)
                $authRequest->setAuthorizationApproved($this->getAuthorizationApprovedAttribute($request));

                // Return the HTTP redirect response
                return $server->completeAuthorizationRequest($authRequest, $response);
            } else {
                return $this->getAuthorizationView($authRequest, $request->getUri()->getQuery());
            }
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))->generateHttpResponse($response);
        }
    }

    public function getAuthorizationView(AuthorizationRequest $authRequest, $queryString)
    {
        $view = property_exists($this, 'authorizationView') ? $this->authorizationView : 'oauth2server::authorize';

        return view($view)
            ->with('authRequest', $authRequest)
            ->with('queryString', $queryString);
    }

    public function getAuthorizationApprovedAttribute(ServerRequestInterface $request)
    {
        $attribute = property_exists($this, 'authorizationApprovedAttribute') ? $this->authorizationApprovedAttribute : 'authorize';

        return (bool) $this->getRequestParameter($attribute, $request, false);
    }

    protected function getRequestParameter($parameter, ServerRequestInterface $request, $default = null)
    {
        $requestParameters = (array) $request->getParsedBody();

        return isset($requestParameters[$parameter]) ? $requestParameters[$parameter] : $default;
    }
}
