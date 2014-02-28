<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\AccessTokenInterface;
use League\OAuth2\Server\Storage\Adapter;
use League\OAuth2\Server\Entity\AccessToken;
use DB;
use Carbon\Carbon;

class FluentAccessToken extends Adapter implements AccessTokenInterface
{
    /**
     * Get an instance of Entites\AccessToken
     * @param  string $token The access token
     * @return \League\OAuth2\Server\Entity\AccessToken
     */
    public function get($token)
    {
        $result = DB::table('oauth_access_tokens')
                ->where('oauth_access_tokens.token', $token)
                ->first();

        if (is_null($result)) {
            return null;
        }

        return new AccessToken($this->getServer())
                 ->setToken($result->token)
                 ->setExpireTime($result->expires);
    }

    public function getByRefreshToken($refreshToken)
    {
        $result = DB::table('oauth_access_tokens')
                ->select('oauth_access_tokens.*')
                ->join('oauth_refresh_tokens', 'oauth_access_tokens.token', '=', 'oauth_refresh_tokens.access_token')
                ->where('oauth_refresh_tokens.token', $refreshToken);

        if (is_null($result)) {
            return null;
        }

        return new AccessToken($this->getServer())
                 ->setToken($result->token)
                 ->setExpireTime($result->expires);
    }

    /**
     * Get the scopes for an access token
     * @param  string $token The access token
     * @return array Array of \League\OAuth2\Server\Entity\Scope
     */
    public function getScopes($token)
    {
        $result = DB::table('oauth_access_token_scopes')
                        ->select('oauth_scopes.*')
                        ->join('oauth_scopes', 'oauth_access_token_scopes.scope_id', '=', 'oauth_scopes.id')
                        ->where('oauth_access_token_scopes.token', $token)
                        ->get();
        
        $scopes = [];
        
        foreach ($result as $scope) {
            $scopes[] = new Scope($this->getServer())
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
            'token' => $token,
            'expires' => $expireTime,
            'session_id' => $sessionId
        ]);

        return new AccessToken($this->getServer())
                 ->setToken($result->token)
                 ->setExpireTime($result->expires);
    }

    /**
     * Associate a scope with an acess token
     * @param  string $token The access token
     * @param  string $scope The scope
     * @return void
     */
    public function associateScope($token, $scope)
    {
        DB::table('oauth_access_token_scopes')->insert([
            'token'      => $token,
            'scope_id'   => $scope,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * Delete an access token
     * @param  string $token The access token to delete
     * @return void
     */
    public function delete($token)
    {
        DB::table('oauth_access_tokens')
                ->where('oauth_access_tokens.token', $token)
                ->delete();
    }
}
