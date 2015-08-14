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

class AccessTokensTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_access_tokens')->delete();

        $datetime = Carbon::now();

        $tokens = [
            [
                'id' => 'totallyanaccesstoken1',
                'session_id' => 1,
                'expire_time' => time() + 60,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'id' => 'totallyanaccesstoken2',
                'session_id' => 2,
                'expire_time' => time() + 120,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_access_tokens')->insert($tokens);
    }
}
