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
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

/**
 * This is the access token model class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class AccessToken extends Model implements AccessTokenEntityInterface
{
    use AccessTokenTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_tokens';

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
     * Set the identifier of the user associated with the token.
     *
     * @param string|int $identifier The identifier of the user
     */
    public function setUserIdentifier($identifier)
    {
        $this->user_id = $identifier;
    }

    /**
     * Get the token user's identifier.
     *
     * @return string|int
     */
    public function getUserIdentifier()
    {
        return $this->user_id;
    }

    /**
     * Get the client that the token was issued to.
     *
     * @return ClientEntityInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the client that the token was issued to.
     *
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->client()->associate($client);
    }

    /**
     * Associate a scope with the token.
     *
     * @param \League\OAuth2\Server\Entities\ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        $this->scopes()->attach($scope);
    }

    /**
     * Return an array of scopes associated with the token.
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes()
    {
        return $this->scopes->toArray();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class, 'oauth_access_token_scopes');
    }

    public function refreshToken()
    {
        return $this->hasOne(RefreshToken::class);
    }
}
