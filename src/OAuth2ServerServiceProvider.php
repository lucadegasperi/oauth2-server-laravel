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
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
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
        $this->bootConfigPublishing();
        $this->bootMigrationPublishing();

        $this->bootGuard();
    }

    protected function registerServer()
    {
        $this->app->singleton(AuthorizationServer::class, function ($app) {

            $server = new AuthorizationServer(
                $app->make(ClientRepositoryInterface::class),
                $app->make(AccessTokenRepositoryInterface::class),
                $app->make(ScopeRepositoryInterface::class),
                new CryptKey($app['config']->get('oauth2.private_key_path'), $app['config']->get('oauth2.key_passphrase')),
                new CryptKey($app['config']->get('oauth2.public_key_path'), $app['config']->get('oauth2.key_passphrase')),
                $app->make($app['config']->get('oauth2.response_type')),
                $app->make($app['config']->get('oauth2.authorization_validator'))
            );

            foreach ($app['config']->get('oauth2.grant_types') as $grantType) {
                $server->enableGrantType(
                    $app->make($grantType['class'], $grantType),
                    new DateInterval('PT' . $grantType['access_token_ttl'] . 'S')
                );
            }

            return $server;

        });

        $this->app->singleton(ResourceServer::class, function ($app) {

            $server = new ResourceServer(
                $app->make(AccessTokenRepositoryInterface::class),
                new CryptKey($app['config']->get('oauth2.public_key_path'), $app['config']->get('oauth2.key_passphrase')),
                $app->make($app['config']->get('oauth2.authorization_validator'))
            );

            return $server;

        });
    }

    protected function registerGrantTypes()
    {
        $this->app->bind(AuthCodeGrant::class, function ($app, $parameters = []) {

            $grant = new AuthCodeGrant(
                $app->make(AuthCodeRepositoryInterface::class),
                $app->make(RefreshTokenRepositoryInterface::class),
                new DateInterval('PT' . $parameters['auth_code_ttl'] . 'S')
            );

            if(array_key_exists($parameters['code_exchange_proof'])) {
                if($parameters['code_exchange_proof'] === true) {
                    $grant->enableCodeExchangeProof();
                }
            }

            return $grant;
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
                $app->make(ResourceServer::class),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    /**
     * Setup the migrations.
     *
     * @return void
     */
    protected function bootMigrationPublishing()
    {
        $source = realpath(__DIR__ . '/../database/migrations/');
        $this->publishes([$source => database_path('migrations')], 'migrations');
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function bootConfigPublishing()
    {
        $source = realpath(__DIR__ . '/../config/oauth2.php');
        $this->publishes([$source => config_path('oauth2.php')]);
        $this->mergeConfigFrom($source, 'oauth2');
    }
}