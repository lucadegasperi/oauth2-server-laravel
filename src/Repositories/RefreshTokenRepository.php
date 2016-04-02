<?php
/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\Interfaces\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use LucaDegasperi\OAuth2Server\Entities\RefreshToken;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

    /**
     * Create a new refresh token_name.
     *
     * @param \League\OAuth2\Server\Entities\Interfaces\RefreshTokenEntityInterface $refreshTokenEntity
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $refreshTokenEntity->save();
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        RefreshToken::where('token', $tokenId)->delete();
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return RefreshToken::where('token', $tokenId)->count() === 0;
    }
}