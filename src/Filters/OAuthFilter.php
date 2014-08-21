<?php
/**
 * OAuth route filter
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Filters;

use League\OAuth2\Server\Exception\InvalidScopeException;
use LucaDegasperi\OAuth2Server\Authorizer;

class OAuthFilter
{
    protected $authorizer;

    protected $httpHeadersOnly = false;

    protected $scopes = [];

    public function __construct(Authorizer $authorizer, $httpHeadersOnly = false)
    {
        $this->authorizer = $authorizer;
        $this->httpHeadersOnly = $httpHeadersOnly;
    }

    public function isHttpHeadersOnly()
    {
        return $this->httpHeadersOnly;
    }

    public function setHttpHeadersOnly($httpHeadersOnly)
    {
        $this->httpHeadersOnly = $httpHeadersOnly;
    }

    /**
     * Run the oauth filter
     *
     * @internal param mixed $route, mixed $request, mixed $scope,...
     * @return void a bad response in case the request is invalid
     */
    public function filter()
    {
        if (func_num_args() > 2) {
            $args = func_get_args();
            $this->scopes = array_slice($args, 2);
        }
        $this->authorizer->validateAccessToken($this->httpHeadersOnly);
        $this->validateScopes();
    }

    public function setScopes(array $scopes)
    {
        $this->scopes = $scopes;
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function validateScopes()
    {
        if (!empty($this->scopes) and !$this->authorizer->hasScope($this->scopes)) {
            throw new InvalidScopeException(implode(',', $this->scopes));
        }
    }
}
