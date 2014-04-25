<?php
/**
 * Fluent storage implementation for an OAuth 2.0 Refresh Token
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\RefreshTokenInterface;
use League\OAuth2\Server\Storage\Adapter;
use League\OAuth2\Server\Entity\RefreshToken;
use DB;

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
                ->where('id', $token)
                ->first();

        if (is_null($result)) {
            return null;
        }

        return (new RefreshToken($this->getServer()))
               ->setToken($result->id)
               ->setExpireTime($result->expire_time);
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
            'id'              => $token,
            'expire_time'     => $expireTime,
            'access_token_id' => $accessToken
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
        ->where('id', $token)
        ->delete();
    }
}
