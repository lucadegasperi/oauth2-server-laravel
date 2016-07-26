<?php

/*
 * This file is part of Laravel OAuth 2.0.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * This is the refresh token model class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class RefreshToken extends Model implements RefreshTokenEntityInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_refresh_tokens';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var string[]
     */
    protected $dates = ['expires_at'];

    /**
     * Get the token's identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->token;
    }

    /**
     * Set the token's identifier.
     *
     * @param $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->token = $identifier;
    }

    /**
     * Get the token's expiry date time.
     *
     * @return \DateTime
     */
    public function getExpiryDateTime()
    {
        return $this->expires_at;
    }

    /**
     * Set the date time when the token expires.
     *
     * @param \DateTime $dateTime
     */
    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->expires_at = Carbon::instance($dateTime);
    }

    /**
     * Set the access token that the refresh token was associated with.
     *
     * @param \League\OAuth2\Server\Entities\AccessTokenEntityInterface $accessToken
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->accessToken()->associate($accessToken);
    }

    /**
     * Get the access token that the refresh token was originally associated with.
     *
     * @return \League\OAuth2\Server\Entities\AccessTokenEntityInterface
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function accessToken()
    {
        return $this->belongsTo(AccessToken::class);
    }
}
