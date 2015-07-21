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

class GrantsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_grants')->delete();

        $datetime = Carbon::now();

        $grants = [
            [
                'id' => 'grant1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'id' => 'grant2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_grants')->insert($grants);

        DB::table('oauth_client_grants')->delete();

        $clientGrants = [
            [
                'client_id' => 'client1id',
                'grant_id' => 'grant1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'client_id' => 'client2id',
                'grant_id' => 'grant2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_client_grants')->insert($clientGrants);
    }
}
