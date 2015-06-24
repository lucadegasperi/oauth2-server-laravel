<?php
/**
 * Service Provider for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server;

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
use LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware;
use LucaDegasperi\OAuth2Server\Middleware\OAuthOwnerMiddleware;

class OAuth2ServerServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
        $this->setupMigrations();
    }
    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/oauth2.php');

        $this->publishes([$source => config_path('oauth2.php')]);

        $this->mergeConfigFrom($source, 'oauth2');
    }
    /**
     * Setup the migrations.
     *
     * @return void
     */
    protected function setupMigrations()
    {
        $source = realpath(__DIR__.'/../database/migrations/');

        $this->publishes([$source => base_path('/database/migrations')], 'migrations');
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->registerAuthorizer();
        $this->registerMiddlewareBindings();
    }

    /**
     * Register the Authorization server with the IoC container
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
                $grant = new $grantParams['class'];
                $grant->setAccessTokenTTL($grantParams['access_token_ttl']);

                if (array_key_exists('callback', $grantParams)) {
                    // list($className, $method) = array_pad(explode('@', $grantParams['callback']), 2, 'verify');
                    // $verifier = $app->make($className);
                    // $grant->setVerifyCredentialsCallback([$verifier, $method]);
                    $grant->setVerifyCredentialsCallback($grantParams['callback']);
                }
                if (array_key_exists('auth_token_ttl', $grantParams)) {
                    $grant->setAuthTokenTTL($grantParams['auth_token_ttl']);
                }
                if (array_key_exists('refresh_token_ttl', $grantParams)) {
                    $grant->setRefreshTokenTTL($grantParams['refresh_token_ttl']);
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

        $this->app->bind(Authorizer::class, function($app)
        {
            return $app['oauth2-server.authorizer'];
        });
    }

    /**
     * Register the Middleware to the IoC container because some middleware need additional parameters
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

        $this->app->bindShared(OAuthOwnerMiddleware::class, function ($app) {
            return new OAuthOwnerMiddleware($app['oauth2-server.authorizer']);
        });
    }

    /**
     * Get the services provided by the provider.
     * @return string[]
     * @codeCoverageIgnore
     */
    public function provides()
    {
        return ['oauth2-server.authorizer'];
    }
}
