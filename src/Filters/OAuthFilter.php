<?php namespace LucaDegasperi\OAuth2Server\Filters;

use ResourceServer;
use Response;
use League\OAuth2\Server\Exception\InvalidAccessTokenException;

class OAuthFilter
{
    protected $httpHeadersOnly = false;

    public function __construct($httpHeadersOnly = false)
    {
        $this->httpHeadersOnly = $httpHeadersOnly;
    }

    public function isHttpHeadersOnly()
    {
        return $this->httpHeadersOnly;
    }

    public function setHttpHeadersOnly($httpHeadersOnly)
    {
        $this->httpHeadersOnly = $httpHeadersOnly;
    }

    /**
     * Run the oauth filter
     *
     * @param Route $route the route being called
     * @param Request $request the request object
     * @param string $scope additional filter arguments
     * @return Response|null a bad response in case the request is invalid
     */
    public function filter($route, $request, $scope = null)
    {
        if (!ResourceServer::isValid($this->httpHeadersOnly)) {
            return Response::json([
                'status' => 403,
                'error' => 'forbidden',
                'error_message' => 'Access token is missing or is expired',
            ], 403);
        }

        if (! is_null($scope)) {
            $scopes = explode(',', $scope);

            if (! ResourceServer::hasScope($scopes)) {
                return Response::json([
                    'status' => 403,
                    'error' => 'forbidden',
                    'error_message' => 'Only access token with scope(s) "'.$scope.'" can use this endpoint',
                ], 403);
            }
        }
    }
}
