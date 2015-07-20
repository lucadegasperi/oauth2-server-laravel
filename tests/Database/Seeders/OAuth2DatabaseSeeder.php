<?php

/**
 * Database Seeder
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Tests\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class OAuth2DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() === 'production') {
            exit('I just stopped you getting fired. Love Luca');
        }

        Model::unguard();

        $this->call(ClientsTableSeeder::class);
        $this->call(GrantsTableSeeder::class);
        $this->call(ScopesTableSeeder::class);
        $this->call(SessionsTableSeeder::class);
        $this->call(AuthCodesTableSeeder::class);
        $this->call(AccessTokensTableSeeder::class);
        $this->call(RefreshTokensTableSeeder::class);

        Model::reguard();
    }
}
