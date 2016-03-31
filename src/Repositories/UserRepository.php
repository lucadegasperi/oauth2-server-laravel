<?php

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

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
        // TODO: Implement getUserEntityByUserCredentials() method.
    }
}