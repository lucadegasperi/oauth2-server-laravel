<?php
/**
 * OAuth parameters check route filter
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Filters;

use League\OAuth2\Server\Exception\ClientException;
use AuthorizationServer;
use Response;
use Session;
use Exception;

class CheckAuthorizationParamsFilter
{

    /**
     * Run the check authorization params filter
     *
     * @param Route $route the route being called
     * @param Request $request the request object
     * @param string $scope additional filter arguments
     * @return Response|null a bad response in case the params are invalid
     */
    public function filter($route, $request, $scope = null)
    {
        try {

            $params = AuthorizationServer::checkAuthorizeParams();

            Session::put('oauth2.authorize-params', $params);


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
