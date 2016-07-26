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

use Illuminate\Database\Eloquent\Model;

/**
 * This is the redirect uri model class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class RedirectUri extends Model
{
    protected $table = 'oauth_client_redirect_uris';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
