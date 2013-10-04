<?php namespace LucaDegasperi\OAuth2Server\Facades;

use Illuminate\Support\Facades\Facade;

class ResourceServerFacade extends Facade {

    protected static function getFacadeAccessor() { return 'oauth2.resource-server'; }

}


