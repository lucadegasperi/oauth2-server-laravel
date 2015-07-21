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
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (OAuthException $e) {
            return new JsonResponse([
                    'error' => $e->errorType,
                    'error_description' => $e->getMessage(),
                ],
                $e->httpStatusCode,
                $e->getHttpHeaders()
            );
        }
    }
}
