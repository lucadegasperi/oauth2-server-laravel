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
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\SessionInterface;

/**
 * This is the fluent session class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class FluentSession extends AbstractFluentAdapter implements SessionInterface
{
    /**
     * Get a session from it's identifier.
     *
     * @param string $sessionId
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function get($sessionId)
    {
        $result = $this->getConnection()->table('oauth_sessions')
            ->where('_id', $sessionId)->first();
        if (is_null($result)) {
            return;
        }

        return (new SessionEntity($this->getServer()))
           ->setId($result['_id'])
           ->setOwner($result['owner_type'], $result['owner_id']);
    }

    /**
     * Get a session from an access token.
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $accessToken The access token
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        $result = $this->getConnection()->table('oauth_access_tokens')
            ->where('id', $accessToken->getId())->first();
        if (is_null($result)) {
            return;
        }

        $result = $this->getConnection()->table('oauth_sessions')
            ->where('_id', $result['session_id'])->first();
        if (is_null($result)) {
            return;
        }

        return (new SessionEntity($this->getServer()))
           ->setId($result['_id'])
           ->setOwner($result['owner_type'], $result['owner_id']);
    }

    /**
     * Get a session's scopes.
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity
     *
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(SessionEntity $session)
    {
        $result = $this->getConnection()->table('oauth_session_scopes')
            ->where('session_id', $session->getId())->get();

        foreach ($result as &$row) {
            $row = $this->getConnection()->table('oauth_scopes')
                ->where('id', $row['scope_id'])->first();
        }

        $scopes = [];

        foreach ($result as $scope) {
            $scopes[] = (new ScopeEntity($this->getServer()))->hydrate([
                'id' => $scope['id'],
                'description' => $scope['description'],
            ]);
        }

        return $scopes;
    }

    /**
     * Create a new session.
     *
     * @param string $ownerType         Session owner's type (user, client)
     * @param string $ownerId           Session owner's ID
     * @param string $clientId          Client ID
     * @param string $clientRedirectUri Client redirect URI (default = null)
     *
     * @return int The session's ID
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
        return $this->getConnection()->table('oauth_sessions')->insertGetId([
            'client_id' => $clientId,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'client_redirect_uri' => $clientRedirectUri,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Associate a scope with a session.
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session
     * @param \League\OAuth2\Server\Entity\ScopeEntity   $scope   The scopes ID might be an integer or string
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $this->getConnection()->table('oauth_session_scopes')->insert([
            'session_id' => $session->getId(),
            'scope_id' => $scope->getId(),
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Get a session from an aut  code.
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $authCode The auth code
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        $result = $this->getConnection()->table('oauth_auth_codes')
            ->where('id', $authCode->getId())->first();
        if (is_null($result)) {
            return;
        }

        $result = $this->getConnection()->table('oauth_sessions')
            ->where('_id', $result['session_id'])->first();
        if (is_null($result)) {
            return;
        }

        return (new SessionEntity($this->getServer()))
           ->setId($result['_id'])
           ->setOwner($result['owner_type'], $result['owner_id']);
    }
}
