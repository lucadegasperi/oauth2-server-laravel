<?php
/**
 * Fluent storage implementation for an OAuth 2.0 Auth Code
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\Adapter;
use League\OAuth2\Server\Storage\AuthCodeInterface;

class FluentAuthCode extends Adapter implements AuthCodeInterface {

    /**
     * Get the auth code
     * @param  string $code
     * @return \League\OAuth2\Server\Entity\AuthCode
     */
    public function get($code)
    {
        // TODO: Implement get() method.
    }

    /**
     * Get the scopes for an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AuthCodeEntity $token)
    {
        // TODO: Implement getScopes() method.
    }

    /**
     * Associate a scope with an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @param  \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     * @return void
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        // TODO: Implement associateScope() method.
    }

    /**
     * Delete an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The access token to delete
     * @return void
     */
    public function delete(AuthCodeEntity $token)
    {
        // TODO: Implement delete() method.
    }
}