<?php


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
        $this->app->bind(ScopeRepositoryInterface::class,        ScopeRepository::class);
        $this->app->bind(UserRepositoryInterface::class,         UserRepositoryInterface::class);
    }
}
