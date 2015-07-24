<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Storage;

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
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerStorageBindings();
        $this->registerInterfaceBindings();
    }

    /**
     * Bind the storage implementations to the IoC container.
     *
     * @return void
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
     *
     * @return void
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
