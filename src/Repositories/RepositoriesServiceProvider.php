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

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AccessTokenRepositoryInterface::class,  AccessTokenRepository::class);
        $this->app->bind(AuthCodeRepositoryInterface::class,     AuthCodeRepository::class);
        $this->app->bind(ClientRepositoryInterface::class,       ClientRepository::class);
        $this->app->bind(RefreshTokenRepositoryInterface::class, RefreshTokenRepository::class);
        $this->app->bind(UserRepositoryInterface::class,         UserRepository::class);
        $this->app->bind(ScopeRepositoryInterface::class, function ($app) {
            return new ScopeRepository($app['config']->get('oauth2.default_scopes', []));
        });
    }
}
