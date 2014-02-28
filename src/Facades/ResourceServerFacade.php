<?php namespace LucaDegasperi\OAuth2Server\Facades;

use Illuminate\Support\Facades\Facade;

class ResourceServerFacade extends Facade
{

    /**
     * Get the registered name of the component
     *
     * @return string
     * @codeCoverageIgnore
     */
    protected static function getFacadeAccessor()
    {
        return 'oauth2.resource-server';
    }
}
