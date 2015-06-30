<?php

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use League\OAuth2\Server\Exception\InvalidScopeException;
use LucaDegasperi\OAuth2Server\Authorizer;

/**
 * Class OAuthMiddleware
 *
 * This middleware checks that the user has an active access_token and optionally that the user currently has a particular
 * or set of particular active scopes.
 *
 * This is an "abstract" middleware. That is, it is meant to be overridden for each particular use case.
 *
 * The only function you need override is the "requiredScopes" function which returns an array of the scopes you want to
 * check. For example, if you wanted a filter that checked for two scopes "basic" and "advanced", then you would create
 * a new middleware say "OAuthBasicAdvancedMiddleware" that extends from this class, and override the `requiredScopes` function
 * to return `[ "basic", "advanced" ]`
 *
 * You would then register each of your middlewares in your HTTP Kernel, and use them as you would any other middleware.
 *
 * @package LucaDegasperi\OAuth2Server\Middleware
 */
abstract class OAuthMiddleware implements Middleware
{

    /**
     * Our Authorizer instance.
     * @var Authorizer
     */
    protected $authorizer;

    /**
     * Do we only want to check HTTP headers for the access token, or is query string acceptable as well?
     *
     * If you wish to change this, simply override the property in your inheriting middleware.
     * @var bool
     */
    protected $httpHeadersOnly = false;

    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * This function is meant to be overridden in any particular middleware you want to use.
     *
     * It should return an array listing the scope names (ids) that you wish to verify are set.
     *
     * @return array
     */
    public abstract function requiredScopes();

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->authorizer->validateAccessToken($this->httpHeadersOnly);
        $this->validateScopes();

        return $next($request);
    }

    /**
     * Validate the scopes
     * @throws \League\OAuth2\Server\Exception\InvalidScopeException
     */
    protected function validateScopes()
    {
        if (!empty($this->requiredScopes()) and !$this->authorizer->hasScope($this->requiredScopes())) {
            throw new InvalidScopeException(implode(',', $this->requiredScopes()));
        }
    }
}
