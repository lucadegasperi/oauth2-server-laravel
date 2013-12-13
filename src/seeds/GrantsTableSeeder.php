<?php

use Carbon\Carbon;

class GrantsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('oauth_grants')->delete();

        $datetime = Carbon::now();

        $grants = array(
            array(
                'grant' => 'grant1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'grant' => 'grant2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_grants')->insert($grants);

        DB::table('oauth_client_grants')->delete();

        $clientGrants = array(
            array(
                'client_id' => 'client1id',
                'grant_id' => 1,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'client_id' => 'client2id',
                'grant_id' => 2,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_client_grants')->insert($clientGrants);
    }

}
