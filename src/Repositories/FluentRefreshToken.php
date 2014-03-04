<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\RefreshTokenInterface;
use League\OAuth2\Server\Storage\Adapter;
use League\OAuth2\Server\Entity\RefreshToken;
use DB;
use Carbon\Carbon;

class FluentRefreshToken extends Adapter implements RefreshTokenInterface
{
    /**
     * Return a new instance of \League\OAuth2\Server\Entity\RefreshToken
     * @param  string $token
     * @return \League\OAuth2\Server\Entity\RefreshToken
     */
    public function get($token)
    {
        $result = DB::table('oauth_refresh_tokens')
                ->where('token', $token)
                ->first();

        if (is_null($result)) {
            return null;
        }

        return (new RefreshToken($this->getServer()))
                 ->setToken($result->token)
                 ->setExpireTime($result->expires);
    }

    /**
     * Create a new refresh token_name
     * @param  string $token
     * @param  integer $expireTime
     * @param  string $accessToken
     * @return \League\OAuth2\Server\Entity\RefreshToken
     */
    public function create($token, $expireTime, $accessToken)
    {
        DB::table('oauth_refresh_tokens')->insert([
            'token' => $token,
            'expires' => $expireTime,
            'access_token' => $accessToken
        ]);

        return (new RefreshToken($this->getServer()))
                 ->setToken($token)
                 ->setExpireTime($expireTime);
    }

    /**
     * Delete the refresh token
     * @param  string $token
     * @return void
     */
    public function delete($token)
    {
        DB::table('oauth_refresh_tokens')
            ->where('token', $token)
            ->delete();
    }
}
