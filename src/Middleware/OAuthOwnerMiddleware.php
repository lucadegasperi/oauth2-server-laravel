<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use League\OAuth2\Server\Exception\AccessDeniedException;
use LucaDegasperi\OAuth2Server\Authorizer;

/**
 * This is the oauth owner middleware class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class OAuthOwnerMiddleware
{
    /**
     * The Authorizer instance.
     *
     * @var \LucaDegasperi\OAuth2Server\Authorizer
     */
    protected $authorizer;

    /**
     * Create a new oauth owner middleware instance.
     *
     * @param \LucaDegasperi\OAuth2Server\Authorizer $authorizer
     */
    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $ownerTypesString
     *
     * @throws \League\OAuth2\Server\Exception\AccessDeniedException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $ownerTypesString = null)
    {
        $ownerTypes = [];

        if (!is_null($ownerTypesString)) {
            $ownerTypes = explode('+', $ownerTypesString);
        }
        
        $this->authorizer->setRequest($request);
        if (!in_array($this->authorizer->getResourceOwnerType(), $ownerTypes)) {
            throw new AccessDeniedException();
        }

        return $next($request);
    }
}
