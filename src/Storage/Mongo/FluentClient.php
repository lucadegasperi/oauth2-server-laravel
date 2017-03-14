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

use Carbon\Carbon;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;

/**
 * This is the fluent client class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class FluentClient extends AbstractFluentAdapter implements ClientInterface
{
    /**
     * Limit clients to grants.
     *
     * @var bool
     */
    protected $limitClientsToGrants = false;

    /**
     * Create a new fluent client instance.
     *
     * @param \Illuminate\Database\ConnectionResolverInterface $resolver
     * @param bool                                             $limitClientsToGrants
     */
    public function __construct(Resolver $resolver, $limitClientsToGrants = false)
    {
        parent::__construct($resolver);
        $this->limitClientsToGrants = $limitClientsToGrants;
    }

    /**
     * Check if clients are limited to grants.
     *
     * @return bool
     */
    public function areClientsLimitedToGrants()
    {
        return $this->limitClientsToGrants;
    }

    /**
     * Whether or not to limit clients to grants.
     *
     * @param bool $limit
     */
    public function limitClientsToGrants($limit = false)
    {
        $this->limitClientsToGrants = $limit;
    }

    /**
     * Get the client.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $grantType
     *
     * @return null|\League\OAuth2\Server\Entity\ClientEntity
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        // Get client
        $where = is_null($clientSecret) ? ['id' => $clientId] : ['id' => $clientId, 'secret' => $clientSecret];
        $client = $this->getConnection()->table('oauth_clients')->where($where)->first();
        if (is_null($client)) {
            return;
        }

        if ($redirectUri) {
            // Get client endpoint
            $where = ['client_id' => (String) $client['_id'], 'redirect_uri' => $redirectUri];
            $endpoint = $this->getConnection()->table('oauth_client_endpoints')->where($where)->first();
            if (is_null($endpoint)) {
                return;
            }
        }

        if ($this->limitClientsToGrants === true && !is_null($grantType)) {
            // Get grant
            $where = ['id' => $grantType];
            $grant = $this->getConnection()->table('oauth_grants')->where($where)->first();
            if (is_null($grant)) {
                return;
            }

            // Get client grant
            $where = ['client_id' => (string) $client['_id'], 'grant_id' => (string) $grant['_id']];
            $clientGrant = $this->getConnection()->table('oauth_client_grants')->where($where)->first();
            if (is_null($clientGrant)) {
                return;
            }
        }

        return $this->hydrateEntity($client);
    }

    /**
     * Get the client associated with a session.
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
     *
     * @return null|\League\OAuth2\Server\Entity\ClientEntity
     */
    public function getBySession(SessionEntity $session)
    {
        $result = $this->getConnection()->table('oauth_sessions')
            ->where('_id', $session->getId())->first();
        if (is_null($result)) {
            return;
        }

        $result = $this->getConnection()->table('oauth_clients')
            ->where('id', $result['client_id'])->first();
        if (is_null($result)) {
            return;
        }

        return $this->hydrateEntity($result);
    }

    /**
     * Create a new client.
     *
     * @param string $name   The client's unique name
     * @param string $id     The client's unique id
     * @param string $secret The clients' unique secret
     *
     * @return string
     */
    public function create($name, $id, $secret)
    {
        return $this->getConnection()->table('oauth_clients')->insertGetId([
            'id' => $id,
            'name' => $name,
            'secret' => $secret,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Hydrate the entity.
     *
     * @param $result
     *
     * @return \League\OAuth2\Server\Entity\ClientEntity
     */
    protected function hydrateEntity($result)
    {
        $client = new ClientEntity($this->getServer());
        $client->hydrate([
            'id' => $result['id'],
            'name' => $result['name'],
            'secret' => $result['secret'],
            'redirectUri' => (isset($result->redirect_uri) ? $result->redirect_uri : null),
        ]);

        return $client;
    }
}
