<?php namespace LucaDegasperi\OAuth2Server;

use Illuminate\Support\ServiceProvider;

class OAuth2ServerServiceProvider extends ServiceProvider {

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
		$this->package('lucadegasperi/oauth2-server-laravel');

		//require_once __DIR__.'/../../filters.php';
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

		$app->bind('League\OAuth2\Server\Storage\ClientInterface',  'LucaDegasperi\OAuth2Server\Repositories\FluentClient');
		$app->bind('League\OAuth2\Server\Storage\ScopeInterface',   'LucaDegasperi\OAuth2Server\Repositories\FluentScope');
		$app->bind('League\OAuth2\Server\Storage\SessionInterface', 'LucaDegasperi\OAuth2Server\Repositories\FluentSession');

		$app['oauth2.authorization-server'] = $app->share(function($app){

			$server = $app->make('League\OAuth2\Server\Authorization');

			// add the supported grant types to the authorization server
			foreach ($app['config']->get('oauth2-server-laravel::oauth2.grant_types') as $grantKey => $grantValue) {
				$server->addGrantType(new $grantValue[0]($server));
				$server->getGrantType($grantKey)->setAccessTokenTTL($grantValue[1]);
			}

			$server->requireStateParam($app['config']->get('oauth2-server-laravel::oauth2.state_param'));

			$server->requireScopeParam($app['config']->get('oauth2-server-laravel::oauth2.scope_param'));

			$server->setScopeDelimeter($app['config']->get('oauth2-server-laravel::oauth2.scope_delimiter'));

			$server->setDefaultScope($app['config']->get('oauth2-server-laravel::oauth2.default_scope'));

			$server->setAccessTokenTTL($app['config']->get('oauth2-server-laravel::oauth2.access_token_ttl'));

			return $server;

		});

		$app['oauth2.resource-server'] = $app->share(function($app){

			$server = $app->make('League\OAuth2\Server\Resource');

			return $server;

		});
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