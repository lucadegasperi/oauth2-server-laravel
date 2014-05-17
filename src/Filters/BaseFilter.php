<?php
/**
 * OAuth base filter
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Filters;

use League\OAuth2\Server\Exception\OAuthException;
use Illuminate\Http\JsonResponse;

abstract class BaseFilter
{
    protected function errorResponse(OAuthException $e)
    {
        return new JsonResponse([
                'error' => $e->errorType,
                'error_message' => $e->getMessage()
            ],
            $e->httpStatusCode,
            $e->getHttpHeaders()
        );
    }
}
 