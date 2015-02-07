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

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
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
        
        Eloquent::unguard();

        $this->call('ClientsTableSeeder');
        $this->call('GrantsTableSeeder');
        $this->call('ScopesTableSeeder');
        $this->call('SessionsTableSeeder');
        $this->call('AuthCodesTableSeeder');
        $this->call('AccessTokensTableSeeder');
        $this->call('RefreshTokensTableSeeder');
    }
}
