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

namespace LucaDegasperi\OAuth2Server\Storage;

use League\OAuth2\Server\Storage\RefreshTokenInterface;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use Carbon\Carbon;

class FluentRefreshToken extends FluentAdapter implements RefreshTokenInterface
{
    /**
     * Return a new instance of \League\OAuth2\Server\Entity\RefreshTokenEntity
     * @param  string $token
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function get($token)
    {
        $result = $this->getConnection()->table('oauth_refresh_tokens')
                ->where('oauth_refresh_tokens.id', $token)
                ->where('oauth_refresh_tokens.expire_time', '>=', time())
                ->first();

        if (is_null($result)) {
            return null;
        }

        return (new RefreshTokenEntity($this->getServer()))
               ->setId($result->id)
               ->setAccessTokenId($result->access_token_id)
               ->setExpireTime((int)$result->expire_time);
    }

    /**
     * Create a new refresh token_name
     * @param  string $token
     * @param  integer $expireTime
     * @param  string $accessToken
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function create($token, $expireTime, $accessToken)
    {
        $this->getConnection()->table('oauth_refresh_tokens')->insert([
            'id'              => $token,
            'expire_time'     => $expireTime,
            'access_token_id' => $accessToken,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return (new RefreshTokenEntity($this->getServer()))
               ->setId($token)
               ->setAccessTokenId($accessToken)
               ->setExpireTime((int)$expireTime);
    }

    /**
     * Delete the refresh token
     * @param  \League\OAuth2\Server\Entity\RefreshTokenEntity $token
     * @return void
     */
    public function delete(RefreshTokenEntity $token)
    {
        $this->getConnection()->table('oauth_refresh_tokens')
        ->where('id', $token->getId())
        ->delete();
    }
}
