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
use LucaDegasperi\OAuth2Server\Decorators\AuthorizationServerDecorator;
use LucaDegasperi\OAuth2Server\Filters\OAuthFilter;
use LucaDegasperi\OAuth2Server\Repositories\FluentClient;
use LucaDegasperi\OAuth2Server\Repositories\FluentScope;
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

        $this->registerAuthorizationServer();
        
        $this->registerResourceServer();

        $this->registerFilterBindings();

        $this->registerCommands();
    }

    /**
     * Bind the repositories to the IoC container
     * @return void
     */
    public function registerRepositoryBindings()
    {
        $this->app->bind('LucaDegasperi\OAuth2Server\Repositories\FluentClient', function ($app) {

            $limitClientsToGrants = $app['config']->get('oauth2-server-laravel::oauth2.limit_clients_to_grants');
            return new FluentClient($limitClientsToGrants);
        });

        $this->app->bind('LucaDegasperi\OAuth2Server\Repositories\FluentScope', function ($app) {

            $limitClientsToScopes = $app['config']->get('oauth2-server-laravel::oauth2.limit_clients_to_scopes');
            $limitScopesToGrants = $app['config']->get('oauth2-server-laravel::oauth2.limit_scopes_to_grants');

            return new FluentScope($limitClientsToScopes, $limitScopesToGrants);
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
    public function registerAuthorizationServer()
    {
        $this->app['oauth2.authorization-server'] = $this->app->share(function ($app) {            

            $config = $app['config']->get('oauth2-server-laravel::oauth2');

            // TODO: add authcode storage
            $server = $app->make('League\OAuth2\Server\AuthorizationServer')
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

                $server->addGrantType($grant);
            }

            $server->setRequest($app['request']);

            return new AuthorizationServerDecorator($server);
        });
    }

    /**
     * Register the ResourceServer with the IoC container
     * @return void
     */
    public function registerResourceServer()
    {
        $this->app['oauth2.resource-server'] = $this->app->share(function ($app) {

            $server = $app->make('League\OAuth2\Server\ResourceServer');

            $server->setRequest($app['request']);

            return $server;

        });
    }

    /**
     * Register the Filters to the IoC container because some filters need additional parameters
     * @return void
     */
    public function registerFilterBindings()
    {
        $this->app->bind('LucaDegasperi\OAuth2Server\Filters\OAuthFilter', function ($app) {
            $httpHeadersOnly = $app['config']->get('oauth2-server-laravel::oauth2.http_headers_only');

            return new OAuthFilter($httpHeadersOnly);
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
        return array('oauth2.authorization-server', 'oauth2.resource-server');
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

    /**
     * Boot the filters
     * @return void
     */
    private function bootFilters()
    {
        $this->app['router']->filter('check-authorization-params', 'LucaDegasperi\OAuth2Server\Filters\CheckAuthorizationParamsFilter');
        $this->app['router']->filter('oauth', 'LucaDegasperi\OAuth2Server\Filters\OAuthFilter');
        $this->app['router']->filter('oauth-owner', 'LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter');
    }
}
