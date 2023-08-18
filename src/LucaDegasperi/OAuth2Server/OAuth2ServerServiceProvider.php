<?php namespace Tikamsah\OAuth2Server;

use Illuminate\Support\ServiceProvider;
use Tikamsah\OAuth2Server\Proxies\AuthorizationServerProxy;

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
        $this->package('tikamsah/oauth2-server-laravel', 'tikamsah/oauth2-server-laravel');

        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];

        // Bind a filter to check if the auth code grant type params are provided
        $router->filter('check-authorization-params', 'Tikamsah\OAuth2Server\Filters\CheckAuthorizationParamsFilter');

        // Bind a filter to make sure that an endpoint is accessible only by authorized members eventually with specific scopes
        $router->filter('oauth', 'Tikamsah\OAuth2Server\Filters\OAuthFilter');

        // Bind a filter to make sure that an endpoint is accessible only by a specific owner
        $router->filter('oauth-owner', 'Tikamsah\OAuth2Server\Filters\OAuthOwnerFilter');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // let's bind the interfaces to the implementations
        $app = $this->app;

        $app->bind('League\OAuth2\Server\Storage\ClientInterface', 'Tikamsah\OAuth2Server\Repositories\FluentClient');
        $app->bind('League\OAuth2\Server\Storage\ScopeInterface', 'Tikamsah\OAuth2Server\Repositories\FluentScope');
        $app->bind('League\OAuth2\Server\Storage\SessionInterface', 'Tikamsah\OAuth2Server\Repositories\FluentSession');
        $app->bind('Tikamsah\OAuth2Server\Repositories\SessionManagementInterface', 'Tikamsah\OAuth2Server\Repositories\FluentSession');

        $app['oauth2.authorization-server'] = $app->share(function ($app) {

            $server = $app->make('League\OAuth2\Server\Authorization');

            $config = $app['config']->get('tikamsah/oauth2-server-laravel::oauth2');

            // add the supported grant types to the authorization server
            foreach ($config['grant_types'] as $grantKey => $grantValue) {

                $server->addGrantType(new $grantValue['class']($server));
                $server->getGrantType($grantKey)->setAccessTokenTTL($grantValue['access_token_ttl']);

                if (array_key_exists('callback', $grantValue)) {
                    $server->getGrantType($grantKey)->setVerifyCredentialsCallback($grantValue['callback']);
                }
                if (array_key_exists('auth_token_ttl', $grantValue)) {
                    $server->getGrantType($grantKey)->setAuthTokenTTL($grantValue['auth_token_ttl']);
                }
                if (array_key_exists('refresh_token_ttl', $grantValue)) {
                    $server->getGrantType($grantKey)->setRefreshTokenTTL($grantValue['refresh_token_ttl']);
                }
                if (array_key_exists('rotate_refresh_tokens', $grantValue)) {
                    $server->getGrantType($grantKey)->rotateRefreshTokens($grantValue['rotate_refresh_tokens']);
                }
            }

            $server->requireStateParam($config['state_param']);

            $server->requireScopeParam($config['scope_param']);

            $server->setScopeDelimeter($config['scope_delimiter']);

            $server->setDefaultScope($config['default_scope']);

            $server->setAccessTokenTTL($config['access_token_ttl']);

            return new AuthorizationServerProxy($server);

        });

        $app['oauth2.resource-server'] = $app->share(function ($app) {

            $server = $app->make('League\OAuth2\Server\Resource');

            return $server;

        });

        $app['oauth2.expired-tokens-command'] = $app->share(function ($app) {
            return $app->make('Tikamsah\OAuth2Server\Commands\ExpiredTokensCommand');
        });

        $this->commands('oauth2.expired-tokens-command');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('oauth2.authorization-server', 'oauth2.resource-server');
    }
}
