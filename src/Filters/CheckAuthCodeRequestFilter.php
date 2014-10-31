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

use LucaDegasperi\OAuth2Server\Authorizer;

class CheckAuthCodeRequestFilter
{
    /**
     * The authorizer instance
     * @var \LucaDegasperi\OAuth2Server\Authorizer
     */
    protected $authorizer;

    /**
     * @param Authorizer $authorizer
     */
    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * Run the check authorization params filter
     *
     * @internal param mixed $route, mixed $request, mixed $scope,...
     * @return Response|null a bad response in case the params are invalid
     */
     public function handle($request, \Closure $next)
    {
        $this->authorizer->checkAuthCodeRequest();

        return $next();
    }
}
