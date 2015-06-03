<?php
/**
 * Base Migration for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Support;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

abstract class AbstractMigration extends Migration
{
    /**
     * Create the migration helper instance.
     *
     * @return void
     */
    public function __construct()
    {
        $database = Config::get('oauth2.database');

        $this->connection = $database !== 'default' ? $database : null;
    }

    /**
     * Setup the schema with a connection.
     *
     * @return mixed
     */
    public function schema()
    {
        return Schema::connection($this->connection);
    }
}
