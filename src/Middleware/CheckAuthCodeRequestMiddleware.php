<?php

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use LucaDegasperi\OAuth2Server\Authorizer;

class CheckAuthCodeRequestMiddleware
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

    public function handle($request, Closure $next)
    {
        $this->authorizer->checkAuthCodeRequest();

        return $next($request);
    }
}
