<?php

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use LucaDegasperi\OAuth2Server\Authorizer;
use League\OAuth2\Server\Exception\AccessDeniedException;

class OAuthOwnerMiddleware
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

    public function handle($request, Closure $next, $ownerTypesString = null)
    {
        $ownerTypes = [];
        if (!is_null($ownerTypesString)) {
            $ownerTypes = explode('+', $ownerTypesString);
        }

        if (!in_array($this->authorizer->getResourceOwnerType(), $ownerTypes)) {
            throw new AccessDeniedException();
        }

        return $next($request);
    }
}
