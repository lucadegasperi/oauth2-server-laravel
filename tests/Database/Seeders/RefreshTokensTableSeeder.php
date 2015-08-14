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

class RefreshTokensTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_refresh_tokens')->delete();

        $datetime = Carbon::now();

        $tokens = [
            [
                'id' => 'totallyarefreshtoken1',
                'access_token_id' => 'totallyanaccesstoken1',
                'expire_time' => time() + 60,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_refresh_tokens')->insert($tokens);
    }
}
