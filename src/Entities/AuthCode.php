<?php
/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @property mixed redirect_uri
 * @property mixed code
 * @property mixed user
 * @property Carbon expires_at
 * @property mixed scopes
 * @property int|string user_id
 * @property mixed client
 */
class AuthCode extends Model implements AuthCodeEntityInterface
{
    protected $table = 'oauth_auth_codes';

    protected $dates = ['expires_at'];

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    /**
     * @param string $uri
     */
    public function setRedirectUri($uri)
    {
        $this->redirect_uri = $uri;
    }

    /**
     * Get the token's identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->code;
    }

    /**
     * Set the token's identifier.
     *
     * @param $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->code = $identifier;
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
        return $this->user->getIdentifier();
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class, 'oauth_auth_code_scopes');
    }
}