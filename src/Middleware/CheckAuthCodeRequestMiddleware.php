<?php

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use LucaDegasperi\OAuth2Server\Authorizer;

/**
 * Class CheckAuthCodeRequestMiddleware
 *
 * This middleware verifies that the proper headers and request parameters are set for an authorization code grant request.
 *
 * It mimics the functionality of the 'check-authorize-params' middleware from the Laravel 4 version of this package. If
 * you wish to maintain backwards-compatibility of your code, register this middleware as a route middleware using the name
 * 'check-authorize-params'.
 *
 * @package LucaDegasperi\OAuth2Server\Middleware
 */
class CheckAuthCodeRequestMiddleware implements Middleware
{
    /**
     * The Authorizer instance
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
     * Handle an incoming request.
     *
     * Will throw if the proper request parameters are not set for the Authorization Code Grant.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \League\OAuth2\Server\Exception\InvalidRequestException
     * @throws \League\OAuth2\Server\Exception\InvalidClientException
     * @throws \League\OAuth2\Server\Exception\UnsupportedResponseTypeException
     *
     */
    public function handle($request, Closure $next)
    {
        $this->authorizer->checkAuthCodeRequest();

        return $next($request);
    }
}
