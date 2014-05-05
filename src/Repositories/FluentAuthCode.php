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
        $result = DB::table('oauth_auth_codes')
            ->where('oauth_auth_codes.id', $code)
            ->first();

        if (is_null($result)) {
            return null;
        }

        return (new AuthCodeEntity($this->getServer()))
            ->setToken($result->id)
            ->setExpireTime($result->expire_time);
    }

    /**
     * Get the scopes for an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AuthCodeEntity $token)
    {
        $result = DB::table('oauth_auth_code_scopes')
            ->select('oauth_scopes.*')
            ->join('oauth_scopes', 'oauth_auth_code_scopes.scope_id', '=', 'oauth_scopes.id')
            ->where('oauth_auth_code_scopes.auth_code_id', $token->getToken())
            ->get();

        $scopes = [];

        foreach ($result as $scope) {
            $scopes[] = (new ScopeEntity($this->getServer()))
                ->setId($scope->id)
                ->setDescription($scope->description);
        }

        return $scopes;
    }

    /**
     * Associate a scope with an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @param  \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     * @return void
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        DB::table('oauth_auth_code_scopes')->insert([
            'auth_code_id'    => $token->getToken(),
            'scope_id'        => $scope->getId(),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now()
        ]);
    }

    /**
     * Delete an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The access token to delete
     * @return void
     */
    public function delete(AuthCodeEntity $token)
    {
        DB::table('oauth_auth_codes')
        ->where('oauth_auth_codes.id', $token->getToken())
        ->delete();
    }
}