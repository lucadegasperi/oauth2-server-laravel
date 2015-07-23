<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Grant;

use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\InvalidRequestException;

/**
 * This is the password grant class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class PasswordGrant extends AbstractGrant
{
    /**
     * Validate the data and check the credentials.
     *
     * @throws \League\OAuth2\Server\Exception\InvalidRequestException
     *
     * @return int|bool
     */
    protected function authenticate()
    {
        $username = $this->server->getRequest()->request->get('username', null);

        if (is_null($username)) {
            throw new InvalidRequestException('username');
        }

        $password = $this->server->getRequest()->request->get('password', null);

        if (is_null($password)) {
            throw new InvalidRequestException('password');
        }

        $credentials = [
            'username' => $username,
            'password' => password,
        ];

        if (Auth::attempt($credentials)) {
            return Auth::user()->id;
        }

        return false;
    }
}
