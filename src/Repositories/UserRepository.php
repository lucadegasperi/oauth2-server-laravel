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

use Illuminate\Contracts\Auth\UserProvider;
use League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @var UserProvider
     */
    private $provider;

    public function __construct(UserProvider $provider)
    {

        $this->provider = $provider;
    }

    /**
     * Get a user entity.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType The grant type used
     * @param \League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface $clientEntity
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\UserEntityInterface
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {

        $credentials = [
            'username' => $username,
            'password' => $password,
        ];

        $user = $this->provider->retrieveByCredentials($credentials);

        if (is_null($user)) {

            return null;
        }

        // TODO: validate grant type and client for user

        return $this->provider->validateCredentials($user, $credentials) ? $user : null;

    }
}