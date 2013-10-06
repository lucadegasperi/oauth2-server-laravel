<?php namespace LucaDegasperi\OAuth2Server\Filters;

use ResourceServer;
use Response;

class OAuthFilter {

    public function filter($route, $request, $scope = null)
    {
        try {
            ResourceServer::isValid(Config::get('oauth2-server-laravel::oauth2.http_headers_only'));
        }
        catch (League\OAuth2\Server\Exception\InvalidAccessTokenException $e) {
            return Response::json(array(
                'status' => 403,
                'error' => 'forbidden',
                'error_message' => $e->getMessage(),
            ), 403);
        }

        if ( ! is_null($scope)) {
            $scopes = explode(',', $scope);

            foreach ($scopes as $s) {
                if ( ! ResourceServer::hasScope($s)) {
                    return Response::json(array(
                        'status' => 403,
                        'error' => 'forbidden',
                        'error_message' => 'Only access token with scope '.$s.' can use this endpoint',
                    ), 403);
                }
            }
        }
    }

}