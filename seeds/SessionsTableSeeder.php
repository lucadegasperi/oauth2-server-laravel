<?php

use Carbon\Carbon;

class SessionsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('oauth_sessions')->delete();

        $datetime = Carbon::now();

        $sessions = array(
            array(
                'client_id' => 'client1id',
                'owner_id'  => '1',
                'owner_type' => 'user',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'client_id' => 'client2id',
                'owner_id'  => '2',
                'owner_type' => 'user',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_sessions')->insert($sessions);

        /*DB::table('oauth_client_scopes')->delete();

        $clientScopes = array(
            array(
                'client_id' => 'client1id',
                'scope_id' => 1,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'client_id' => 'client2id',
                'scope_id' => 2,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_client_scopes')->insert($clientScopes);

        DB::table('oauth_grant_scopes')->delete();

        $grantScopes = array(
            array(
                'grant_id' => 1,
                'scope_id' => 1,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'grant_id' => 2,
                'scope_id' => 2,
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_grant_scopes')->insert($grantScopes);*/
    }

}
