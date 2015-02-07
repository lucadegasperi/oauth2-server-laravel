<?php
/**
 * Access Tokens Table Seeder
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AccessTokensTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_access_tokens')->delete();

        $datetime = Carbon::now();

        $tokens = [
            [
                'id' => 'totallyanaccesstoken1',
                'session_id'  => 1,
                'expire_time' => time() + 60,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'id' => 'totallyanaccesstoken2',
                'session_id'  => 2,
                'expire_time' => time() + 120,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_access_tokens')->insert($tokens);
    }
}
