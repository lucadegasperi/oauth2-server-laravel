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

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScopesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_scopes')->delete();

        $datetime = Carbon::now();

        $scopes = [
            [
                'id' => 'scope1',
                'description' => 'Scope 1 Description',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'id' => 'scope2',
                'description' => 'Scope 2 Description',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_scopes')->insert($scopes);

        DB::table('oauth_client_scopes')->delete();

        $clientScopes = [
            [
                'client_id' => 'client1id',
                'scope_id' => 'scope1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'client_id' => 'client2id',
                'scope_id' => 'scope2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_client_scopes')->insert($clientScopes);

        DB::table('oauth_grant_scopes')->delete();

        $grantScopes = [
            [
                'grant_id' => 'grant1',
                'scope_id' => 'scope1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'grant_id' => 'grant2',
                'scope_id' => 'scope2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_grant_scopes')->insert($grantScopes);
    }
}
