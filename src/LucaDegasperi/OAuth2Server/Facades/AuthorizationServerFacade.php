<?php namespace LucaDegasperi\OAuth2Server\Facades;

use Illuminate\Support\Facades\Facade;

class AuthorizationServerFacade extends Facade {

    protected static function getFacadeAccessor() { return 'oauth2.authorization-server'; }

}


