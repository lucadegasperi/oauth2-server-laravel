<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Support;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

/**
 * This is the abstract migration class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
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
