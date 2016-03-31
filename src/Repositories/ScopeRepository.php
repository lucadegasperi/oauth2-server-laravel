<?php

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface;
use League\OAuth2\Server\Entities\Interfaces\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use LucaDegasperi\OAuth2Server\Entities\Scope;

class ScopeRepository implements ScopeRepositoryInterface
{

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        Scope::where('identifier', $identifier)->first();
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param \League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface $clientEntity
     * @param null|string $userIdentifier
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        if (!$clientEntity->has('scopes')) {
            return $scopes;
        }
    }
}