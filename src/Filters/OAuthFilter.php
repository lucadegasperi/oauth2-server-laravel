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
    /**
     * The Authorizer instance
     * @var \LucaDegasperi\OAuth2Server\Authorizer
     */
    protected $authorizer;

    /**
     * Whether or not to check the http headers only for an access token
     * @var bool
     */
    protected $httpHeadersOnly = false;

    /**
     * The scopes to check for
     * @var array
     */
    protected $scopes = [];

    /**
     * @param Authorizer $authorizer
     * @param bool $httpHeadersOnly
     */
    public function __construct(Authorizer $authorizer, $httpHeadersOnly = false)
    {
        $this->authorizer = $authorizer;
        $this->httpHeadersOnly = $httpHeadersOnly;
    }

    /**
     * Whether or not the filter will check only the http headers for an access token
     * @return bool
     */
    public function isHttpHeadersOnly()
    {
        return $this->httpHeadersOnly;
    }

    /**
     * Whether or not the filter should check only the http headers for an access token
     * @param $httpHeadersOnly
     */
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

    /**
     * Set the scopes to which the filter should check for
     * @param array $scopes
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * The scopes to which the filter should check for
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Validate the scopes
     * @throws \League\OAuth2\Server\Exception\InvalidScopeException
     */
    public function validateScopes()
    {
        if (!empty($this->scopes) and !$this->authorizer->hasScope($this->scopes)) {
            throw new InvalidScopeException(implode(',', $this->scopes));
        }
    }
}
