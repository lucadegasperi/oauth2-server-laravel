<?php
/**
 * OAuth route filter
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Filters;

use ResourceServer;
use Response;

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
     * @internal param mixed $route, mixed $request, mixed $scope,...
     * @return Response|void a bad response in case the request is invalid
     */
    public function filter()
    {
        if (!ResourceServer::isValid($this->httpHeadersOnly)) {
            return Response::json([
                'status' => 401,
                'error' => 'unauthorized',
                'error_message' => 'Access token is missing or is expired',
            ], 401);
        }

        if (func_num_args() > 2) {
            $args = func_get_args();
            $scopes = array_slice($args, 2);

            if (!ResourceServer::hasScope($scopes)) {
                return Response::json([
                    'status' => 403,
                    'error' => 'forbidden',
                    'error_message' => 'Only access token with scope(s) "' . implode(', ', $scopes) . '" can use this endpoint',
                ], 403);
            }
        }
    }
}
