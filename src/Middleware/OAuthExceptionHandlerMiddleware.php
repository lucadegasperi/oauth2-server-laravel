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
use Illuminate\Http\JsonResponse;
use League\OAuth2\Server\Exception\OAuthException;

/**
 * This is the exception handler middleware class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class OAuthExceptionHandlerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	    $response = $next($request);

            // Was an OAuthException previously caught by the pipeline? If so, hijack response, replacing with json error.
            if (isset($response->exception) && $response->exception instanceof OAuthException) {

                $data = [
                    'error' => $response->exception->errorType,
                    'error_description' => $response->exception->getMessage(),
                ];

                return new JsonResponse($data, $response->exception->httpStatusCode, $response->exception->getHttpHeaders());
            }

            return $response;
    }
}
