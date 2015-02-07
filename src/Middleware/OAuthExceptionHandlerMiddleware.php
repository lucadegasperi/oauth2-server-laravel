<?php

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\Middleware;
use League\OAuth2\Server\Exception\OAuthException;

/*
* OAuthExceptionHandlerMiddleware
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
