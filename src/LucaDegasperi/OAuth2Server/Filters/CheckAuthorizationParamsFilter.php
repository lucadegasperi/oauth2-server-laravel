<?php namespace Tikamsah\OAuth2Server\Filters;

use AuthorizationServer;
use Response;
use Session;
use League\OAuth2\Server\Exception\ClientException;
use Exception;

class CheckAuthorizationParamsFilter
{

    /**
     * Run the check authorization params filter
     *
     * @param Route $route the route being called
     * @param Request $request the request object
     * @return Response|null a bad response in case the params are invalid
     */
    public function filter($route, $request)
    {
        try {

            $params = AuthorizationServer::checkAuthorizeParams();

            Session::put('authorize-params', $params);


        } catch (ClientException $e) {

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
    }
}
