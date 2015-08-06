<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
