<?php
/**
 * Refresh Tokens Table Seeder
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

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
