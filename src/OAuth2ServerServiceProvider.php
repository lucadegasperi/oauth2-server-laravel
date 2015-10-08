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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use League\OAuth2\Server\Storage\AuthCodeInterface;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Storage\RefreshTokenInterface;
use League\OAuth2\Server\Storage\ScopeInterface;
use League\OAuth2\Server\Storage\SessionInterface;
use LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware;
use LucaDegasperi\OAuth2Server\Middleware\OAuthClientOwnerMiddleware;
use LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware;
use LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware;

/**
 * This is the oauth2 server service provider class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class OAuth2ServerServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig($this->app);
        $this->setupMigrations($this->app);
    }

    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function setupConfig(Application $app)
    {
        $source = realpath(__DIR__.'/../config/oauth2.php');

        if (class_exists('Illuminate\Foundation\Application', false) && $app->runningInConsole()) {
            $this->publishes([$source => config_path('oauth2.php')]);
        } elseif (class_exists('Laravel\Lumen\Application', false)) {
            $app->configure('oauth2');
        }

        $this->mergeConfigFrom($source, 'oauth2');
    }

    /**
     * Setup the migrations.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function setupMigrations(Application $app)
    {
        $source = realpath(__DIR__.'/../database/migrations/');

        if (class_exists('Illuminate\Foundation\Application', false) && $app->runningInConsole()) {
            $this->publishes([$source => database_path('migrations')], 'migrations');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthorizer();
        $this->registerMiddlewareBindings();
    }

    /**
     * Register the Authorization server with the IoC container.
     *
     * @return void
     */
    public function registerAuthorizer()
    {
        $this->app->bindShared('oauth2-server.authorizer', function ($app) {
            $config = $app['config']->get('oauth2');
            $issuer = $app->make(AuthorizationServer::class)
                ->setClientStorage($app->make(ClientInterface::class))
                ->setSessionStorage($app->make(SessionInterface::class))
                ->setAuthCodeStorage($app->make(AuthCodeInterface::class))
                ->setAccessTokenStorage($app->make(AccessTokenInterface::class))
                ->setRefreshTokenStorage($app->make(RefreshTokenInterface::class))
                ->setScopeStorage($app->make(ScopeInterface::class))
                ->requireScopeParam($config['scope_param'])
                ->setDefaultScope($config['default_scope'])
                ->requireStateParam($config['state_param'])
                ->setScopeDelimiter($config['scope_delimiter'])
                ->setAccessTokenTTL($config['access_token_ttl']);

            // add the supported grant types to the authorization server
            foreach ($config['grant_types'] as $grantIdentifier => $grantParams) {
                $grant = $app->make($grantParams['class']);
                $grant->setAccessTokenTTL($grantParams['access_token_ttl']);

                if (array_key_exists('callback', $grantParams)) {
                    list($className, $method) = array_pad(explode('@', $grantParams['callback']), 2, 'verify');
                    $verifier = $app->make($className);
                    $grant->setVerifyCredentialsCallback([$verifier, $method]);
                }

                if (array_key_exists('auth_token_ttl', $grantParams)) {
                    $grant->setAuthTokenTTL($grantParams['auth_token_ttl']);
                }

                if (array_key_exists('refresh_token_ttl', $grantParams)) {
                    $grant->setRefreshTokenTTL($grantParams['refresh_token_ttl']);
                }

                if (array_key_exists('rotate_refresh_tokens', $grantParams)) {
                    $grant->setRefreshTokenRotation($grantParams['rotate_refresh_tokens']);
                }

                $issuer->addGrantType($grant);
            }

            $checker = $app->make(ResourceServer::class);

            $authorizer = new Authorizer($issuer, $checker);
            $authorizer->setRequest($app['request']);
            $authorizer->setTokenType($app->make($config['token_type']));

            $app->refresh('request', $authorizer, 'setRequest');

            return $authorizer;
        });

        $this->app->bind(Authorizer::class, function ($app) {
            return $app['oauth2-server.authorizer'];
        });
    }

    /**
     * Register the Middleware to the IoC container because
     * some middleware need additional parameters.
     *
     * @return void
     */
    public function registerMiddlewareBindings()
    {
        $this->app->bindShared(CheckAuthCodeRequestMiddleware::class, function ($app) {
            return new CheckAuthCodeRequestMiddleware($app['oauth2-server.authorizer']);
        });

        $this->app->bindShared(OAuthMiddleware::class, function ($app) {
            $httpHeadersOnly = $app['config']->get('oauth2.http_headers_only');

            return new OAuthMiddleware($app['oauth2-server.authorizer'], $httpHeadersOnly);
        });

        $this->app->bindShared(OAuthClientOwnerMiddleware::class, function ($app) {
            return new OAuthClientOwnerMiddleware($app['oauth2-server.authorizer']);
        });

        $this->app->bindShared(OAuthUserOwnerMiddleware::class, function ($app) {
            return new OAuthUserOwnerMiddleware($app['oauth2-server.authorizer']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     * @codeCoverageIgnore
     */
    public function provides()
    {
        return ['oauth2-server.authorizer'];
    }
}
