<?php

namespace LucaDegasperi\OAuth2Server\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Http\JsonResponse;
use League\OAuth2\Server\Exception\OAuthException;

/*
* JSONErrorHandlerMiddleware
*/
class JSONErrorHandlerMiddleware implements Middleware
{

    public function handle($request, Closure $next)
    {

        try {
    
            return $next($request);

        } catch (OAuthException $e) {

            // catch any OAuthException and return the results as JSON
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
