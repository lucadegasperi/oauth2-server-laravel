<?php
/**
 * Auth Codes Table Seeder
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AuthCodesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_auth_codes')->delete();

        $datetime = Carbon::now();

        $codes = [
            [
                'id' => 'totallyanauthcode1',
                'session_id'  => 1,
                'redirect_uri' => 'https://example1.com/',
                'expire_time' => time() + 60,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'id' => 'totallyanauthcode2',
                'session_id'  => 2,
                'redirect_uri' => 'https://example2.com/',
                'expire_time' => time() + 120,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_auth_codes')->insert($codes);
    }
}
