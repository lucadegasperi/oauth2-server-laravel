<?php namespace LucaDegasperi\OAuth2Server\Support;

use Illuminate\Database\Migration as BaseMigration;

abstract class Migration extends BaseMigration {

    /**
     * The OAuth2 server database connection name.
     * 
     * @var string
     */
    protected $connection;
  
    /**
     * Create a OAuthMigration instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->connection = Config::get('oauth2-server-laravel::oauth2.database');
    }

}