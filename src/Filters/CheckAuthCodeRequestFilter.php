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

use League\OAuth2\Server\Exception\OAuthException;
use Illuminate\Support\Facades\Response;
use LucaDegasperi\OAuth2Server\Authorizer;
use LucaDegasperi\OAuth2Server\Delegates\AuthCodeCheckerDelegate;

class CheckAuthCodeRequestFilter implements AuthCodeCheckerDelegate
{
    protected $authorizer;

    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

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
        return $this->authorizer->checkAuthCodeRequest($this);
    }

    public function checkSuccessful()
    {
        return null;
    }

    public function checkFailed(OAuthException $e)
    {
        return Response::json(
            [
                'error' => $e->errorType,
                'error_message' => $e->getMessage()
            ],
            $e->httpStatusCode,
            $e->getHttpHeaders()
       );
    }
}
