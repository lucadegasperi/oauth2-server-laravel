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

use League\OAuth2\Server\Entities\Interfaces\AccessTokenEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use LucaDegasperi\OAuth2Server\Entities\AccessToken;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{

    /**
     * Persists a new access token to permanent storage.
     *
     * @param \League\OAuth2\Server\Entities\Interfaces\AccessTokenEntityInterface $accessTokenEntity
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $accessTokenEntity->save();
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        AccessToken::where('token', $tokenId)->delete();
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return AccessToken::where('token', $tokenId)->count() === 0;
    }
}