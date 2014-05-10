<?php
/**
 * Laravel Service Provider for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server;

use Illuminate\Support\ServiceProvider;
use LucaDegasperi\OAuth2Server\Filters\CheckAuthCodeRequestFilter;
use LucaDegasperi\OAuth2Server\Filters\OAuthFilter;
use LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter;
use LucaDegasperi\OAuth2Server\Repositories\FluentClient;
use LucaDegasperi\OAuth2Server\Repositories\FluentScope;
use LucaDegasperi\OAuth2Server\Repositories\FluentAccessToken;
use LucaDegasperi\OAuth2Server\Repositories\FluentAuthCode;
use LucaDegasperi\OAuth2Server\Repositories\FluentRefreshToken;
use LucaDegasperi\OAuth2Server\Repositories\FluentSession;
use LucaDegasperi\OAuth2Server\Console\MigrationsCommand;
use LucaDegasperi\OAuth2Server\Console\OAuthControllerCommand;

class OAuth2ServerServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lucadegasperi/oauth2-server-laravel', 'oauth2-server-laravel', __DIR__.'/../');

        $this->bootFilters();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositoryBindings();
        $this->registerInterfaceBindings();
        $this->registerAuthorizer();
        $this->registerFilterBindings();
        $this->registerCommands();
        $this->registerResolvers();
    }

    /**
     * Bind the repositories to the IoC container
     * @return void
     */
    public function registerRepositoryBindings()
    {
        $provider = $this;

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Repositories\FluentAccessToken', function ($app) use ($provider) {
            return new FluentAccessToken($provider->getConnection());
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Repositories\FluentAuthCode', function ($app) use ($provider) {
            return new FluentAuthCode($provider->getConnection());
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Repositories\FluentClient', function ($app) use ($provider) {
            $limitClientsToGrants = $app['config']->get('oauth2-server-laravel::oauth2.limit_clients_to_grants');
            return new FluentClient($provider->getConnection(), $limitClientsToGrants);
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Repositories\FluentRefreshToken', function ($app) use ($provider) {
            return new FluentRefreshToken($provider->getConnection());
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Repositories\FluentScope', function ($app) use ($provider) {
            $limitClientsToScopes = $app['config']->get('oauth2-server-laravel::oauth2.limit_clients_to_scopes');
            $limitScopesToGrants = $app['config']->get('oauth2-server-laravel::oauth2.limit_scopes_to_grants');
            return new FluentScope($provider->getConnection(), $limitClientsToScopes, $limitScopesToGrants);
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Repositories\FluentSession', function ($app) use ($provider) {
            return new FluentSession($provider->getConnection());
        });
    }

    /**
     * Bind the interfaces to their implementations
     * @return void
     */
    public function registerInterfaceBindings()
    {
        $this->app->bind('League\OAuth2\Server\Storage\ClientInterface',       'LucaDegasperi\OAuth2Server\Repositories\FluentClient');
        $this->app->bind('League\OAuth2\Server\Storage\ScopeInterface',        'LucaDegasperi\OAuth2Server\Repositories\FluentScope');
        $this->app->bind('League\OAuth2\Server\Storage\SessionInterface',      'LucaDegasperi\OAuth2Server\Repositories\FluentSession');
        $this->app->bind('League\OAuth2\Server\Storage\AuthCodeInterface',     'LucaDegasperi\OAuth2Server\Repositories\FluentAuthCode');
        $this->app->bind('League\OAuth2\Server\Storage\AccessTokenInterface',  'LucaDegasperi\OAuth2Server\Repositories\FluentAccessToken');
        $this->app->bind('League\OAuth2\Server\Storage\RefreshTokenInterface', 'LucaDegasperi\OAuth2Server\Repositories\FluentRefreshToken');
    }

    /**
     * Register the Authorization server with the IoC container
     * @return void
     */
    public function registerAuthorizer()
    {
        $this->app->bindShared('oauth2-server.authorizer', function ($app) {
            $config = $app['config']->get('oauth2-server-laravel::oauth2');
            $issuer = $app->make('League\OAuth2\Server\AuthorizationServer')
                          ->setClientStorage($app->make('League\OAuth2\Server\Storage\ClientInterface'))
                          ->setSessionStorage($app->make('League\OAuth2\Server\Storage\SessionInterface'))
                          ->setAuthCodeStorage($app->make('League\OAuth2\Server\Storage\AuthCodeInterface'))
                          ->setAccessTokenStorage($app->make('League\OAuth2\Server\Storage\AccessTokenInterface'))
                          ->setRefreshTokenStorage($app->make('League\OAuth2\Server\Storage\RefreshTokenInterface'))
                          ->setScopeStorage($app->make('League\OAuth2\Server\Storage\ScopeInterface'))
                          ->requireScopeParam($config['scope_param'])
                          ->setDefaultScope($config['default_scope'])
                          ->requireStateParam($config['state_param'])
                          ->setScopeDelimeter($config['scope_delimiter'])
                          ->setAccessTokenTTL($config['access_token_ttl']);

            // add the supported grant types to the authorization server
            foreach ($config['grant_types'] as $grantIdentifier => $grantParams) {
                $grant = new $grantParams['class'];
                $grant->setAccessTokenTTL($grantParams['access_token_ttl']);

                if (array_key_exists('callback', $grantParams)) {
                    $grant->setVerifyCredentialsCallback($grantParams['callback']);
                }
                if (array_key_exists('auth_code_ttl', $grantParams)) {
                    $grant->setAuthCodeTTL($grantParams['auth_code_ttl']);
                }
                if (array_key_exists('refresh_token_ttl', $grantParams)) {
                    $grant->setRefreshTokenTTL($grantParams['refresh_token_ttl']);
                }
                $issuer->addGrantType($grant);
            }

            $checker = $app->make('League\OAuth2\Server\ResourceServer');

            $authorizer = new Authorizer($issuer, $checker);
            $authorizer->setRequest($app['request']);

            $app->refresh('request', $authorizer, 'setRequest');

            return $authorizer;
        });

        $this->app->bind('LucaDegasperi\OAuth2Server\Authorizer', function($app)
        {
            return $app['oauth2-server.authorizer'];
        });
    }

    /**
     * Register the Filters to the IoC container because some filters need additional parameters
     * @return void
     */
    public function registerFilterBindings()
    {
        $this->app->bindShared('LucaDegasperi\OAuth2Server\Filters\CheckAuthCodeRequestFilter', function ($app) {
            return new CheckAuthCodeRequestFilter($app['oauth2-server.authorizer']);
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Filters\OAuthFilter', function ($app) {
            $httpHeadersOnly = $app['config']->get('oauth2-server-laravel::oauth2.http_headers_only');
            return new OAuthFilter($app['oauth2-server.authorizer'], $httpHeadersOnly);
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter', function ($app) {
            return new OAuthOwnerFilter($app['oauth2-server.authorizer']);
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

    /**
     * Registers some utility commands with artisan
     * @return void
     */
    public function registerCommands()
    {
        $this->app->bindShared('command.oauth2-server.controller', function($app) {
            return new OAuthControllerCommand($app['files']);
        });

        $this->app->bindShared('command.oauth2-server.migrations', function() {
            return new MigrationsCommand();
        });

        $this->commands('command.oauth2-server.controller', 'command.oauth2-server.migrations');
    }

    private function registerResolvers()
    {
        $app = $this->app;
        /*$app->resolvingAny(function ($object) use ($app) {
            if ($object instanceof FluentAdapter) {
                $name = $app['config']->get('oauth2-server-laravel::oauth2.database');
                if ($name === 'default') {
                    $name = null;
                }
                $object->setConnection($name);
            }
        });*/

    }

    /**
     * Boot the filters
     * @return void
     */
    private function bootFilters()
    {
        $this->app['router']->filter('check-authorization-params', 'LucaDegasperi\OAuth2Server\Filters\CheckAuthCodeRequestFilter');
        $this->app['router']->filter('oauth', 'LucaDegasperi\OAuth2Server\Filters\OAuthFilter');
        $this->app['router']->filter('oauth-owner', 'LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter');
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        $connectionName = ($this->app['config']->get('oauth2-server-laravel::oauth2.database') !== 'default') ? $this->app['config']->get('oauth2-server-laravel::oauth2.database') : null;
        return $this->app['db']->connection($connectionName);
    }

}
