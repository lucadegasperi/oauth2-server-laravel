<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Storage;

use Carbon\Carbon;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\RefreshTokenInterface;

/**
 * This is the fluent refresh token class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class FluentRefreshToken extends AbstractFluentAdapter implements RefreshTokenInterface
{
    /**
     * Return a new instance of \League\OAuth2\Server\Entity\RefreshTokenEntity.
     *
     * @param string $token
     *
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function get($token)
    {
        $result = $this->getConnection()->table('oauth_refresh_tokens')
                ->where('oauth_refresh_tokens.id', $token)
                ->where('oauth_refresh_tokens.expire_time', '>=', time())
                ->first();

        if (is_null($result)) {
            return;
        }

        return (new RefreshTokenEntity($this->getServer()))
               ->setId($result->id)
               ->setAccessTokenId($result->access_token_id)
               ->setExpireTime((int) $result->expire_time);
    }

    /**
     * Create or update a refresh token
     *
     * @param  string $token
     * @param  int $expireTime
     * @param  string $accessToken
     *
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function create($token, $expireTime, $accessToken)
    {
        // We need to know if there is a record with the same token on the table.
        $refreshToken = $this->get($token);

        // If the token already exits then update it, if not update the record.
        if( empty($refreshToken) )
        {
            // Here we create a new refresh token
            $this->getConnection()->table('oauth_refresh_tokens')->insert([
                'id' => $token,
                'expire_time' => $expireTime,
                'access_token_id' => $accessToken,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        else
        {
            // Update the refresh token that corresponds with the token.
            $this->getConnection()->table('oauth_refresh_tokens')
            ->where( 'id', $token )
            ->update([
                'expire_time' => $expireTime,
                'access_token_id' => $accessToken,
                'updated_at' => Carbon::now(),
            ]);
        }

        // We return a RefreshTokenEntity with the tokens data.
        return (new RefreshTokenEntity($this->getServer()))
               ->setId($token)
               ->setAccessTokenId($accessToken)
               ->setExpireTime((int) $expireTime);
    }

    /**
     * Delete the refresh token.
     *
     * @param  \League\OAuth2\Server\Entity\RefreshTokenEntity $token
     *
     * @return void
     */
    public function delete(RefreshTokenEntity $token)
    {
        $this->getConnection()->table('oauth_refresh_tokens')
        ->where('id', $token->getId())
        ->delete();
    }
}
