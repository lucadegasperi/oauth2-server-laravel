<?php namespace LucaDegasperi\OAuth2Server\Filters;

use AuthorizationServer;
use Response;
use Session;

class CheckAuthorizationParamsFilter {

    public function filter($route, $request, $scope = null)
    {
        try {

            $params = AuthorizationServer::getGrantType('authorization_code')->checkAuthoriseParams();

            Session::put('client_id', $params['client_id']);
            Session::put('client_details', $params['client_details']);
            Session::put('redirect_uri', $params['redirect_uri']);
            Session::put('response_type', $params['response_type']);
            Session::put('scopes', $params['scopes']);
            Session::put('state', $params['state']);


        } catch (\League\OAuth2\Server\Exception\ClientException $e) {

            return Response::json(array(
                'status' => 400,
                'error' => 'bad_request',
                'error_message' => $e->getMessage(),
            ), 400);

        } catch (\Exception $e) {

            return Response::json(array(
                'status' => 500,
                'error' => 'internal_server_error',
                'error_message' => 'Internal Server Error',
            ), 500);
        }
    }
}