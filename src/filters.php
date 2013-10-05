<?php

Route::filter('check-authorization-params', function($route, $request, $scope = null)
{
    try {
        
        $params = AuthorizationServer::getGrantType('authorization_code')->checkAuthoriseParams();

        Session::put('client_id', $params['client_id']);
        Session::put('client_details', $params['client_details']);
        Session::put('redirect_uri', $params['redirect_uri']);
        Session::put('response_type', $params['response_type']);
        Session::put('scopes', $params['scopes']);
        Session::put('state', $params['state']);


    } catch (League\OAuth2\Server\Exception\ClientException $e) {

        return Response::json(array(
            'status' => 400,
            'error' => 'bad_request',
            'error_message' => $e->getMessage(),
        ), 400);

    } catch (Exception $e) {

        return Response::json(array(
            'status' => 500,
            'error' => 'internal_server_error',
            'error_message' => 'Internal Server Error',
        ), 500);
    }

});

// make sure an endpoint is accessible only by authrized members eventually with specific scopes 
Route::filter('oauth', function($route, $request, $scope = null)
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

});

// make sure an endpoint is accessible only by a specific owner
Route::filter('oauth-owner', function($route, $request, $scope = null)
{
    if ( ! is_null($scope) and ResourceServer::getOwnerType() !== $scope){
        return Response::json(array(
            'status' => 403,
            'error' => 'forbidden',
            'error_message' => 'Only access tokens representing '.$scope.' can use this endpoint',
        ), 403);
    }
});