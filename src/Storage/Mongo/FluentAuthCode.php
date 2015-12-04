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
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AuthCodeInterface;

/**
 * This is the fluent auth code class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class FluentAuthCode extends AbstractFluentAdapter implements AuthCodeInterface
{
    /**
     * Get the auth code.
     *
     * @param string $code
     *
     * @return \League\OAuth2\Server\Entity\AuthCodeEntity
     */
    public function get($code)
    {
        $result = $this->getConnection()->table('oauth_auth_codes')
            ->where('id', $code)
            ->where('expire_time', '>=', time())
            ->first();

        if (is_null($result)) {
            return;
        }

        return (new AuthCodeEntity($this->getServer()))
            ->setId($result['id'])
            ->setRedirectUri($result['redirect_uri'])
            ->setExpireTime((int) $result['expire_time']);
    }

    /**
     * Get the scopes for an access token.
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     *
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AuthCodeEntity $token)
    {
        $result = $this->getConnection()->table('oauth_auth_code_scopes')
                ->where('auth_code_id', $token->getId())
                ->get();

        foreach ($result as &$row) {
            $row = $this->getConnection()->table('oauth_scopes')
                    ->where('id', $row['scope_id'])
                    ->first();
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
     * Associate a scope with an access token.
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @param \League\OAuth2\Server\Entity\ScopeEntity    $scope The scope
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        $this->getConnection()->table('oauth_auth_code_scopes')->insert([
            'auth_code_id' => $token->getId(),
            'scope_id' => $scope->getId(),
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Delete an access token.
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $token The access token to delete
     */
    public function delete(AuthCodeEntity $token)
    {
        $this->getConnection()->table('oauth_auth_codes')
        ->where('id', $token->getId())
        ->delete();
        $this->getConnection()->table('oauth_auth_code_scopes')
        ->where('auth_code_id', $token->getId())
        ->delete();
    }

    /**
     * Create an auth code.
     *
     * @param string $token       The token ID
     * @param int    $expireTime  Token expire time
     * @param int    $sessionId   Session identifier
     * @param string $redirectUri Client redirect uri
     */
    public function create($token, $expireTime, $sessionId, $redirectUri)
    {
        $this->getConnection()->table('oauth_auth_codes')->insert([
            'id' => $token,
            'session_id' => $sessionId,
            'redirect_uri' => $redirectUri,
            'expire_time' => $expireTime,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
