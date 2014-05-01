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

use LucaDegasperi\OAuth2Server\Delegates\AccessTokenValidatorDelegate;
use LucaDegasperi\OAuth2Server\Authorizer;
use Response;

class OAuthFilter implements AccessTokenValidatorDelegate
{
    protected $authorizer;

    protected $httpHeadersOnly = false;

    protected $scopes = [];

    public function __construct(Authorizer $authorizer, $httpHeadersOnly = false)
    {
        $this->authorizer = $authorizer;
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
        if (func_num_args() > 2) {
            $args = func_get_args();
            $this->scopes = array_slice($args, 2);
        }

        return $this->authorizer->validateAccessToken($this, $this->httpHeadersOnly);
    }

    public function accessTokenValidated()
    {
        if (!empty($this->scopes) and !$this->authorizer->hasScope($this->scopes)) {
            return Response::json([
                'status' => 403,
                'error' => 'forbidden',
                'error_message' => 'Only access token with scope(s) "' . implode(', ', $this->scopes) . '" can use this endpoint',
            ], 403);
        }
        return null;
    }

    public function accessTokenValidationFailed()
    {
        return Response::json([
            'status' => 401,
            'error' => 'unauthorized',
            'error_message' => 'Access token is missing or is expired',
        ], 401);
    }
}
