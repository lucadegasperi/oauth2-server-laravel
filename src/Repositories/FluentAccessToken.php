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

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Entity\AbstractToken;
use League\OAuth2\Server\Entity\RefreshToken;
use League\OAuth2\Server\Entity\Scope;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use League\OAuth2\Server\Storage\Adapter;
use League\OAuth2\Server\Entity\AccessToken;
use DB;
use Carbon\Carbon;

class FluentAccessToken extends Adapter implements AccessTokenInterface
{
    /**
     * Get an instance of Entities\AccessToken
     * @param  string $token The access token
     * @return \League\OAuth2\Server\Entity\AccessToken
     */
    public function get($token)
    {
        $result = DB::table('oauth_access_tokens')
                ->where('oauth_access_tokens.id', $token)
                ->first();

        if (is_null($result)) {
            return null;
        }

        return (new AccessToken($this->getServer()))
               ->setToken($result->id)
               ->setExpireTime($result->expire_time);
    }


    public function getByRefreshToken(RefreshToken $refreshToken)
    {
        $result = DB::table('oauth_access_tokens')
                ->select('oauth_access_tokens.*')
                ->join('oauth_refresh_tokens', 'oauth_access_tokens.id', '=', 'oauth_refresh_tokens.access_token_id')
                ->where('oauth_refresh_tokens.id', $refreshToken->getToken());

        if (is_null($result)) {
            return null;
        }

        return (new AccessToken($this->getServer()))
               ->setToken($result->id)
               ->setExpireTime($result->expire_time);
    }

    /**
     * Get the scopes for an access token
     * @param \League\OAuth2\Server\Entity\AbstractToken $token The access token
     * @return array Array of \League\OAuth2\Server\Entity\Scope
     */
    public function getScopes(AbstractToken $token)
    {
        $result = DB::table('oauth_access_token_scopes')
                ->select('oauth_scopes.*')
                ->join('oauth_scopes', 'oauth_access_token_scopes.scope_id', '=', 'oauth_scopes.id')
                ->where('oauth_access_token_scopes.access_token_id', $token->getToken())
                ->get();
        
        $scopes = [];
        
        foreach ($result as $scope) {
            $scopes[] = (new Scope($this->getServer()))
                      ->setId($scope->id)
                      ->setDescription($scope->description);
        }
        
        return $scopes;
    }

    /**
     * Creates a new access token
     * @param  string $token The access token
     * @param  integer $expireTime The expire time expressed as a unix timestamp
     * @param  string|integer $sessionId The session ID
     * @return \League\OAuth2\Server\Entity\AccessToken
     */
    public function create($token, $expireTime, $sessionId)
    {
        DB::table('oauth_access_tokens')->insert([
            'id' => $token,
            'expire_time' => $expireTime,
            'session_id' => $sessionId
        ]);

        return (new AccessToken($this->getServer()))
               ->setToken($token)
               ->setExpireTime($expireTime);
    }

    /**
     * Associate a scope with an access token
     * @param \League\OAuth2\Server\Entity\AbstractToken $token The access token
     * @param \League\OAuth2\Server\Entity\Scope $scope The scope
     * @return void
     */
    public function associateScope(AbstractToken $token, Scope $scope)
    {
        DB::table('oauth_access_token_scopes')->insert([
            'access_token_id' => $token->getToken(),
            'scope_id'        => $scope->getId(),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now()
        ]);
    }

    /**
     * Delete an access token
     * @param \League\OAuth2\Server\Entity\AbstractToken $token The access token to delete
     * @return void
     */
    public function delete(AbstractToken $token)
    {
        DB::table('oauth_access_tokens')
        ->where('oauth_access_tokens.id', $token->getToken())
        ->delete();
    }
}
