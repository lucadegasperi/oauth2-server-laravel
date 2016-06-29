<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 29/06/16
 * Time: 16:18
 */

namespace LucaDegasperi\OAuth2Server\Traits;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\Auth;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;


trait OAuthControllerTrait
{
    public function postAccessToken(ServerRequestInterface $request, AuthorizationServer $server)
    {
        $response = new Response();

        try {
            return $server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (\Exception $e) {
            // Unknown exception
            $body = new Stream('php://temp', 'r+');
            $body->write($e->getMessage());

            return $response->withStatus(500)->withBody($body);
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
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);
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
        return $this->getRequestParameter($attribute, $request, false);
    }

    protected function getRequestParameter($parameter, ServerRequestInterface $request, $default = null)
    {
        $requestParameters = (array)$request->getParsedBody();

        return isset($requestParameters[$parameter]) ? $requestParameters[$parameter] : $default;
    }
}