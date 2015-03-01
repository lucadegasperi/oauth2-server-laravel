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

use Config;
use Schema;
use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration
{
    /**
     * The OAuth2 server database connection name.
     * 
     * @var string
     */
    protected $connectionName;

    /**
     *
     */
    public function __construct()
    {
        $this->connectionName = (Config::get('oauth2.database') !== 'default') ? Config::get('oauth2.database') : null;
    }

    public function schema()
    {
        return Schema::connection($this->connectionName);
    }
}
