<?php

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\Middleware;
use League\OAuth2\Server\Exception\OAuthException;

/**
 * Class OAuthExceptionHandlerMiddleware
 *
 * This middleware should sit in the global middleware space of your HTTP Kernel. It converts any uncaught OAuthException
 * into a JSON response, which is the best fit for the general case of an OAuth server.
 *
 * If you're registering your own general middleware to catch OAuth Exceptions, remember to put your middleware above this
 * one in your Kernel's middleware chain.
 *
 * @package LucaDegasperi\OAuth2Server\Middleware
 */
class OAuthExceptionHandlerMiddleware implements Middleware
{
    public function handle($request, Closure $next)
    {
        try {

            return $next($request);

        } catch (OAuthException $e) {

            return new JsonResponse([
                    'error'             => $e->errorType,
                    'error_description' => $e->getMessage()
                ],
                $e->httpStatusCode,
                $e->getHttpHeaders()
            );
        }
    }
}
