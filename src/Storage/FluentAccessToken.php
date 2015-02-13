<?php
/**
 * Fluent storage implementation for an OAuth 2.0 Access Token
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Storage;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use Carbon\Carbon;

class FluentAccessToken extends FluentAdapter implements AccessTokenInterface
{
    /**
     * Get an instance of Entities\AccessToken
     * @param  string $token The access token
     * @return null|AbstractTokenEntity
     */
    public function get($token)
    {
        $result = $this->getConnection()->table('oauth_access_tokens')
                ->where('oauth_access_tokens.id', $token)
                ->first();

        if (is_null($result)) {
            return null;
        }

        return (new AccessTokenEntity($this->getServer()))
               ->setId($result->id)
               ->setExpireTime((int)$result->expire_time);
    }


    /*public function getByRefreshToken(RefreshTokenEntity $refreshToken)
    {
        $result = $this->getConnection()->table('oauth_access_tokens')
                ->select('oauth_access_tokens.*')
                ->join('oauth_refresh_tokens', 'oauth_access_tokens.id', '=', 'oauth_refresh_tokens.access_token_id')
                ->where('oauth_refresh_tokens.id', $refreshToken->getId())
                ->first();

        if (is_null($result)) {
            return null;
        }

        return (new AccessTokenEntity($this->getServer()))
               ->setId($result->id)
               ->setExpireTime((int)$result->expire_time);
    }*/

    /**
     * Get the scopes for an access token
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AccessTokenEntity $token)
    {
        $result = $this->getConnection()->table('oauth_access_token_scopes')
                ->select('oauth_scopes.*')
                ->join('oauth_scopes', 'oauth_access_token_scopes.scope_id', '=', 'oauth_scopes.id')
                ->where('oauth_access_token_scopes.access_token_id', $token->getId())
                ->get();
        
        $scopes = [];
        
        foreach ($result as $scope) {
            $scopes[] = (new ScopeEntity($this->getServer()))->hydrate([
               'id' => $scope->id,
                'description' => $scope->description
            ]);
        }
        
        return $scopes;
    }

    /**
     * Creates a new access token
     * @param  string $token The access token
     * @param  integer $expireTime The expire time expressed as a unix timestamp
     * @param  string|integer $sessionId The session ID
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity
     */
    public function create($token, $expireTime, $sessionId)
    {
        $this->getConnection()->table('oauth_access_tokens')->insert([
            'id' => $token,
            'expire_time' => $expireTime,
            'session_id' => $sessionId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return (new AccessTokenEntity($this->getServer()))
               ->setId($token)
               ->setExpireTime((int)$expireTime);
    }

    /**
     * Associate a scope with an access token
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     * @param \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     * @return void
     */
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
    {
        $this->getConnection()->table('oauth_access_token_scopes')->insert([
            'access_token_id' => $token->getId(),
            'scope_id'        => $scope->getId(),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now()
        ]);
    }

    /**
     * Delete an access token
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token to delete
     * @return void
     */
    public function delete(AccessTokenEntity $token)
    {
        $this->getConnection()->table('oauth_access_tokens')
        ->where('oauth_access_tokens.id', $token->getId())
        ->delete();
    }
}
