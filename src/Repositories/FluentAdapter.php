<?php
/**
 * Fluent storage implementation for an OAuth 2.0 Access Token
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\Adapter;
use DB;
use Config;

abstract class FluentAdapter extends Adapter
{
    protected $connectionName = null;

    public function setConnection($name = null)
    {
        $this->connectionName = $name;
    }

    protected function getConnection()
    {
        return DB::connection($this->connectionName);
    }
} 