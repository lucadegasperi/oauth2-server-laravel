<?php

namespace LucaDegasperi\OAuth2Server\Entities;

use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\Interfaces\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Interfaces\RefreshTokenEntityInterface;
use Carbon\Carbon;

/**
 * @property string token
 * @property Carbon expires_at
 * @property mixed accessToken
 */
class RefreshToken extends Model implements RefreshTokenEntityInterface
{

    protected $table = 'oauth_refresh_tokens';

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
     * @param \League\OAuth2\Server\Entities\Interfaces\AccessTokenEntityInterface $accessToken
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->accessToken()->save($accessToken);
    }

    /**
     * Get the access token that the refresh token was originally associated with.
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\AccessTokenEntityInterface
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Has the token expired?
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at->lt(new Carbon());
    }


    public function accessToken()
    {
        return $this->belongsTo(AccessToken::class);
    }
}