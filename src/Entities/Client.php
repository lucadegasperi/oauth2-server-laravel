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
use League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface;

/**
 * @property mixed id
 * @property mixed name
 * @property string redirect_uri
 * @property string identifier
 */
class Client extends Model implements ClientEntityInterface
{
    protected $table = 'oauth_clients';

    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set the client's identifier.
     *
     * @param $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the client's name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set the client's redirect uri.
     *
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirect_uri = $redirectUri;
    }

    /**
     * Returns the registered redirect URI.
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }


    public function accessTokens()
    {
        return $this->hasMany(AccessToken::class);
    }

    public function authCodes()
    {
        return $this->hasMany(AuthCode::class);
    }

    public function scopes()
    {
        return $this->belongsToMany(Scope::class, 'oauth_client_scopes');
    }
}