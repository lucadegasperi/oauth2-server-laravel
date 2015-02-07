<?php
/**
 * Sessions Table Seeder
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SessionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_sessions')->delete();

        $datetime = Carbon::now();

        $sessions = [
            [
                'client_id' => 'client1id',
                'owner_id'  => '1',
                'owner_type' => 'user',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'client_id' => 'client2id',
                'owner_id'  => '2',
                'owner_type' => 'user',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_sessions')->insert($sessions);
    }
}
