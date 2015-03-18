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

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use LucaDegasperi\OAuth2Server\Filters\CheckAuthCodeRequestFilter;
use LucaDegasperi\OAuth2Server\Filters\OAuthFilter;
use LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter;

class OAuth2ServerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     * @return void
     */
    public function boot()
    {
        $this->bootFilters();
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->registerAssets();
        $this->registerAuthorizer();
        $this->registerFilterBindings();
    }

    /**
     * Register the assets to be published
     * @return void
     */
    public function registerAssets()
    {
        $configPath = __DIR__ . '/../config/oauth2.php';
        $mFrom = __DIR__ . '/../migrations/';
        $mTo = $this->app['path.database'] . '/migrations/';
        $this->mergeConfigFrom($configPath, 'oauth2');
        $this->publishes([$configPath => config_path('oauth2.php')], 'config');
        $this->publishes([
            $mFrom . '2015_03_14_084201_create_oauth_scopes_table.php'              => base_path('database/migrations/2015_03_14_084201_create_oauth_scopes_table.php'),
            $mFrom . '2015_03_14_084202_create_oauth_grants_table.php'              => base_path('database/migrations/2015_03_14_084202_create_oauth_grants_table.php'),
            $mFrom . '2015_03_14_084203_create_oauth_grant_scopes_table.php'        => base_path('database/migrations/2015_03_14_084203_create_oauth_grant_scopes_table.php'),
            $mFrom . '2015_03_14_084204_create_oauth_clients_table.php'             => base_path('database/migrations/2015_03_14_084204_create_oauth_clients_table.php'),
            $mFrom . '2015_03_14_084205_create_oauth_client_endpoints_table.php'    => base_path('database/migrations/2015_03_14_084205_create_oauth_client_endpoints_table.php'),
            $mFrom . '2015_03_14_084206_create_oauth_client_scopes_table.php'       => base_path('database/migrations/2015_03_14_084206_create_oauth_client_scopes_table.php'),
            $mFrom . '2015_03_14_084207_create_oauth_client_grants_table.php'       => base_path('database/migrations/2015_03_14_084207_create_oauth_client_grants_table.php'),
            $mFrom . '2015_03_14_084208_create_oauth_sessions_table.php'            => base_path('database/migrations/2015_03_14_084208_create_oauth_sessions_table.php'),
            $mFrom . '2015_03_14_084209_create_oauth_session_scopes_table.php'      => base_path('database/migrations/2015_03_14_084209_create_oauth_session_scopes_table.php'),
            $mFrom . '2015_03_14_084210_create_oauth_auth_codes_table.php'          => base_path('database/migrations/2015_03_14_084210_create_oauth_auth_codes_table.php'),
            $mFrom . '2015_03_14_084211_create_oauth_auth_code_scopes_table.php'    => base_path('database/migrations/2015_03_14_084211_create_oauth_auth_code_scopes_table.php'),
            $mFrom . '2015_03_14_084212_create_oauth_access_tokens_table.php'       => base_path('database/migrations/2015_03_14_084212_create_oauth_access_tokens_table.php'),
            $mFrom . '2015_03_14_084213_create_oauth_access_token_scopes_table.php' => base_path('database/migrations/2015_03_14_084213_create_oauth_access_token_scopes_table.php'),
            $mFrom . '2015_03_14_084214_create_oauth_refresh_tokens_table.php'      => base_path('database/migrations/2015_03_14_084214_create_oauth_refresh_tokens_table.php'),
        ], 'migrations');
    }

    protected function ts($seconds)
    {
        return Carbon::now()->addSeconds($seconds)->format('Y_m_d_His').'_';
    }

    /**
     * Register the Authorization server with the IoC container
     * @return void
     */
    public function registerAuthorizer()
    {
        $this->app->bindShared('oauth2-server.authorizer', function ($app) {
            $config = $app['config']->get('oauth2');
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
                          ->setScopeDelimiter($config['scope_delimiter'])
                          ->setAccessTokenTTL($config['access_token_ttl']);

            // add the supported grant types to the authorization server
            foreach ($config['grant_types'] as $grantIdentifier => $grantParams) {
                $grant = new $grantParams['class'];
                $grant->setAccessTokenTTL($grantParams['access_token_ttl']);

                if (array_key_exists('callback', $grantParams)) {
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

            $checker = $app->make('League\OAuth2\Server\ResourceServer');

            $authorizer = new Authorizer($issuer, $checker);
            $authorizer->setRequest($app['request']);
            $authorizer->setTokenType($app->make($config['token_type']));

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
            $httpHeadersOnly = $app['config']->get('oauth2.http_headers_only');
            return new OAuthFilter($app['oauth2-server.authorizer'], $httpHeadersOnly);
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter', function ($app) {
            return new OAuthOwnerFilter($app['oauth2-server.authorizer']);
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
}
