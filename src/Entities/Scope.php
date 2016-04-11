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

use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @property mixed id
 * @property mixed identifier
 */
class Scope extends Model implements ScopeEntityInterface
{

    protected $table = 'oauth_scopes';

    /**
     * Get the scope's identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set the scope's identifier.
     *
     * @param $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function accessTokens()
    {
        return $this->belongsToMany(AccessToken::class, 'oauth_access_token_scopes');
    }

    public function authCodes()
    {
        return $this->belongsToMany(AuthCode::class, 'oauth_auth_code_scopes');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'oauth_client_scopes');
    }
}