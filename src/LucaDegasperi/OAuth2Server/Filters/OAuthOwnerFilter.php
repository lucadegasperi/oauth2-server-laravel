<?php namespace LucaDegasperi\OAuth2Server\Filters;

use Response;

class OAuthOwnerFilter {

    public function filter($route, $request, $scope = null)
    {
        if ( ! is_null($scope) and ResourceServer::getOwnerType() !== $scope){
            return Response::json(array(
                'status' => 403,
                'error' => 'forbidden',
                'error_message' => 'Only access tokens representing '.$scope.' can use this endpoint',
            ), 403);
        }
    }

}