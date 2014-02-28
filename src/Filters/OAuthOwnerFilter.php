<?php namespace LucaDegasperi\OAuth2Server\Filters;

use ResourceServer;
use Response;

class OAuthOwnerFilter
{
    /**
     * Run the oauth owner filter
     *
     * @param Route $route the route being called
     * @param Request $request the request object
     * @param string $scope the allowed owners
     * @return Response|null a bad response in case the owner is not authorized
     */
    public function filter($route, $request, $scope = null)
    {
        if (! is_null($scope) and ResourceServer::getOwnerType() !== $scope) {
            return Response::json(array(
                'status' => 403,
                'error' => 'forbidden',
                'error_message' => 'Only access tokens owned by a '.$scope.' can use this endpoint',
            ), 403);
        }
    }
}
