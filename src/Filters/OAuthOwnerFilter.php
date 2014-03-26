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
    public function filter()
    {
        if (func_num_args() > 2) {
            $ownerTypes = array_slice(func_get_args(), 2);
            if (!in_array(ResourceServer::getOwnerType(), $ownerTypes)) {
                return Response::json(array(
                    'status' => 403,
                    'error' => 'forbidden',
                    'error_message' => 'Only access tokens owned by a ' . implode(', ', $ownerTypes) . ' can use this endpoint',
                ), 403);
            }
        }
    }
}
