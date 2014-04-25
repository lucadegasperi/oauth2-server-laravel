<?php
/**
 * Grants Table Seeder
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

use Carbon\Carbon;

class GrantsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('oauth_grants')->delete();

        $datetime = Carbon::now();

        $grants = array(
            array(
                'id' => 'grant1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'id' => 'grant2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_grants')->insert($grants);

        DB::table('oauth_client_grants')->delete();

        $clientGrants = array(
            array(
                'client_id' => 'client1id',
                'grant_id' => 'grant1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'client_id' => 'client2id',
                'grant_id' => 'grant2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_client_grants')->insert($clientGrants);
    }

}
