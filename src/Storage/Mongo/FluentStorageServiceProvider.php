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

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use League\OAuth2\Server\Storage\AuthCodeInterface;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Storage\RefreshTokenInterface;
use League\OAuth2\Server\Storage\ScopeInterface;
use League\OAuth2\Server\Storage\SessionInterface;

/**
 * This is the fluent storage service provider class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class FluentStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerStorageBindings();
        $this->registerInterfaceBindings();
    }

    /**
     * Bind the storage implementations to the IoC container.
     */
    public function registerStorageBindings()
    {
        $provider = $this;

        $this->app->bindShared(FluentAccessToken::class, function () use ($provider) {
            $storage = new FluentAccessToken($provider->app['db']);
            $storage->setConnectionName($provider->getConnectionName());

            return $storage;
        });

        $this->app->bindShared(FluentAuthCode::class, function () use ($provider) {
            $storage = new FluentAuthCode($provider->app['db']);
            $storage->setConnectionName($provider->getConnectionName());

            return $storage;
        });

        $this->app->bindShared(FluentClient::class, function ($app) use ($provider) {
            $limitClientsToGrants = $app['config']->get('oauth2.limit_clients_to_grants');
            $storage = new FluentClient($provider->app['db'], $limitClientsToGrants);
            $storage->setConnectionName($provider->getConnectionName());

            return $storage;
        });

        $this->app->bindShared(FluentRefreshToken::class, function () use ($provider) {
            $storage = new FluentRefreshToken($provider->app['db']);
            $storage->setConnectionName($provider->getConnectionName());

            return $storage;
        });

        $this->app->bindShared(FluentScope::class, function ($app) use ($provider) {
            $limitClientsToScopes = $app['config']->get('oauth2.limit_clients_to_scopes');
            $limitScopesToGrants = $app['config']->get('oauth2.limit_scopes_to_grants');
            $storage = new FluentScope($provider->app['db'], $limitClientsToScopes, $limitScopesToGrants);
            $storage->setConnectionName($provider->getConnectionName());

            return $storage;
        });

        $this->app->bindShared(FluentSession::class, function () use ($provider) {
            $storage = new FluentSession($provider->app['db']);
            $storage->setConnectionName($provider->getConnectionName());

            return $storage;
        });
    }

    /**
     * Bind the interfaces to their implementations.
     */
    public function registerInterfaceBindings()
    {
        $this->app->bind(ClientInterface::class, FluentClient::class);
        $this->app->bind(ScopeInterface::class, FluentScope::class);
        $this->app->bind(SessionInterface::class, FluentSession::class);
        $this->app->bind(AuthCodeInterface::class, FluentAuthCode::class);
        $this->app->bind(AccessTokenInterface::class, FluentAccessToken::class);
        $this->app->bind(RefreshTokenInterface::class, FluentRefreshToken::class);
    }

    /**
     * @return string
     */
    public function getConnectionName()
    {
        return ($this->app['config']->get('oauth2.database') !== 'default') ? $this->app['config']->get('oauth2.database') : null;
    }
}
