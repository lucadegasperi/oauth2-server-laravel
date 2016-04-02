<?php
/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server;

use DateInterval;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Server;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class OAuth2ServerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGrantTypes();
        $this->registerServer();
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootGuard();
    }

    protected function registerServer()
    {
        $this->app->singleton(Server::class, function ($app) {

            $server = new Server(
                $app->make(ClientRepositoryInterface::class),
                $app->make(AccessTokenRepositoryInterface::class),
                $app->make(ScopeRepositoryInterface::class),
                $app['config']->get('oauth2.private_key_path'),
                $app['config']->get('oauth2.public_key_path'),
                $app->make($app['config']->get('oauth2.response_type')),
                $app->make($app['config']->get('oauth2.authorization_validator'))
            );

            foreach ($app['config']->get('oauth2.grant_types') as $grantType) {
                $server->enableGrantType(
                    $app->make($grantType['class'], $grantType),
                    new DateInterval('PT' . $grantType['access_token_ttl'] . 'S')
                );
            }

        });
    }

    protected function registerGrantTypes()
    {
        $this->app->bind(AuthCodeGrant::class, function ($app, $parameters = []) {

            return new AuthCodeGrant(
                $app->make(AuthCodeRepositoryInterface::class),
                $app->make(RefreshTokenRepositoryInterface::class),
                $app->make(UserRepositoryInterface::class),
                new DateInterval('PT' . $parameters['auth_code_ttl'] . 'S')
            );

        });

        $this->app->bind(ImplicitGrant::class, function ($app, $parameters = []) {

            return new ImplicitGrant(
                $app->make(UserRepositoryInterface::class)
            );

        });

        $this->app->bind(PasswordGrant::class, function ($app, $parameters = []) {

            return new PasswordGrant(
                $app->make(UserRepositoryInterface::class),
                $app->make(RefreshTokenRepositoryInterface::class)
            );

        });

        $this->app->bind(RefreshTokenGrant::class, function ($app, $parameters = []) {

            return new RefreshTokenGrant(
                $app->make(RefreshTokenRepositoryInterface::class)
            );

        });
    }

    protected function bootGuard()
    {
        $this->app['auth']->extend('oauth', function ($app, $name, array $config) {
            $guard = new Guard(
                $app['auth']->createUserProvider($config['provider']),
                $app->make(Server::class),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}