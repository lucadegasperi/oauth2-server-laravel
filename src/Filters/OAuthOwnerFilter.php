<?php
/**
 * OAuth owner route filter
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Filters;

use LucaDegasperi\OAuth2Server\Authorizer;
use Illuminate\Support\Facades\Response;

class OAuthOwnerFilter
{
    protected $authorizer;

    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * Run the OAuth owner filter
     *
     * @internal param mixed $route, mixed $request, mixed $scope,...
     * @return Response|void a bad response in case the request is invalid
     */
    public function filter()
    {
        if (func_num_args() > 2) {
            $ownerTypes = array_slice(func_get_args(), 2);
            if (!in_array($this->authorizer->getResourceOwnerType(), $ownerTypes)) {
                return Response::json(array(
                    'status' => 403,
                    'error' => 'forbidden',
                    'error_message' => 'Only access tokens owned by a ' . implode(', ', $ownerTypes) . ' can use this endpoint',
                ), 403);
            }
        }
        return null;
    }
}
