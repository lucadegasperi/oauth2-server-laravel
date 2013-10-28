<?php namespace LucaDegasperi\OAuth2Server\Facades;

use Illuminate\Support\Facades\Facade;

class AuthorizationServerFacade extends Facade
{

    /**
     * Get the registered name of the component
     *
     * @return string
     * @codeCoverageIgnore
     */
    protected static function getFacadeAccessor()
    {
        return 'oauth2.authorization-server';
    }
}
