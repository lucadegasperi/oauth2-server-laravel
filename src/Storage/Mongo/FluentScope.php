<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 Choy Peng Kong <pk@vanitee.com>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LucaDegasperi\OAuth2Server\Storage\Mongo;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\ScopeInterface;

/**
 * This is the fluent scope class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class FluentScope extends AbstractFluentAdapter implements ScopeInterface
{
    /*
     * Limit clients to scopes.
     *
     * @var bool
     */
    protected $limitClientsToScopes = false;

    /*
     * Limit scopes to grants.
     *
     * @var bool
     */
    protected $limitScopesToGrants = false;

    /**
     * Create a new fluent scope instance.
     *
     * @param \Illuminate\Database\ConnectionResolverInterface $resolver
     * @param bool|false                                       $limitClientsToScopes
     * @param bool|false                                       $limitScopesToGrants
     */
    public function __construct(Resolver $resolver, $limitClientsToScopes = false, $limitScopesToGrants = false)
    {
        parent::__construct($resolver);
        $this->limitClientsToScopes = $limitClientsToScopes;
        $this->limitScopesToGrants = $limitScopesToGrants;
    }

    /**
     * Set limit clients to scopes.
     *
     * @param bool|false $limit
     */
    public function limitClientsToScopes($limit = false)
    {
        $this->limitClientsToScopes = $limit;
    }

    /**
     * Set limit scopes to grants.
     *
     * @param bool|false $limit
     */
    public function limitScopesToGrants($limit = false)
    {
        $this->limitScopesToGrants = $limit;
    }

    /**
     * Check if clients are limited to scopes.
     *
     * @return bool|false
     */
    public function areClientsLimitedToScopes()
    {
        return $this->limitClientsToScopes;
    }

    /**
     * Check if scopes are limited to grants.
     *
     * @return bool|false
     */
    public function areScopesLimitedToGrants()
    {
        return $this->limitScopesToGrants;
    }

    /**
     * Return information about a scope.
     *
     * Example SQL query:
     *
     * <code>
     * SELECT * FROM oauth_scopes WHERE scope = :scope
     * </code>
     *
     * @param string $scope     The scope
     * @param string $grantType The grant type used in the request (default = "null")
     * @param string $clientId  The client id used for the request (default = "null")
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity|null If the scope doesn't exist return false
     */
    public function get($scopeId, $grantType = null, $clientId = null)
    {
        // Get scope
        $scope = $this->getConnection()->table('oauth_scopes')
            ->where(['id' => $scopeId])->first();

        if (is_null($scope)) {
            return;
        }

        if ($this->limitClientsToScopes === true && !is_null($clientId)) {
            // Get client
            $client = $this->getConnection()->table('oauth_clients')
                ->where(['id' => $clientId])->first();
            if (is_null($client)) {
                return;
            }

            // Get client scope
            $clientScope = $this->getConnection()->table('oauth_client_scopes')
                ->where(['client_id' => (String) $client['_id'], 'scope_id' => (String) $scope['_id']])->first();
            if (is_null($clientScope)) {
                return;
            }
        }

        if ($this->limitScopesToGrants === true && !is_null($grantType)) {
            // Get grant
            $grant = $this->getConnection()->table('oauth_grants')
                ->where(['id' => $grantType])->first();
            if (is_null($grant)) {
                return;
            }

            // Get grant scope
            $grantScope = $this->getConnection()->table('oauth_grant_scopes')
                ->where(['grant_id' => (String) $grant['_id'], 'scope_id' => (String) $scope['_id']])->first();
            if (is_null($grantScope)) {
                return;
            }
        }

        $scopeEntity = new ScopeEntity($this->getServer());
        $scopeEntity->hydrate([
            'id' => $scope['id'],
            'description' => $scope['description'],
        ]);

        return $scopeEntity;
    }
}
