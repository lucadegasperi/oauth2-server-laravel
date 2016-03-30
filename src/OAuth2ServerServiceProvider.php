<?php


namespace LucaDegasperi\OAuth2Server;

use DateInterval;
use Illuminate\Support\ServiceProvider;
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
                    $app->make($grantType['class']),
                    new DateInterval('PT' . $grantType['ttl'] . 'S')
                );
            }

        });
    }

    protected function registerGrantTypes()
    {

    }
}