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

		require_once __DIR__.'/../../filters.php';
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

			$config = $app['config']->get('oauth2-server-laravel::oauth2');

			// add the supported grant types to the authorization server
			foreach ($config['grant_types'] as $grantKey => $grantValue) {
				$server->addGrantType(new $grantValue['class']($server));
				$server->getGrantType($grantKey)->setAccessTokenTTL($grantValue['access_token_ttl']);
			}

			$server->requireStateParam($config['state_param']);

			$server->requireScopeParam($config['scope_param']);

			$server->setScopeDelimeter($config['scope_delimiter']);

			$server->setDefaultScope($config['default_scope']);

			$server->setAccessTokenTTL($config['access_token_ttl']);

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