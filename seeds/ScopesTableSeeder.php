<?php

use Carbon\Carbon;

class ScopesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('oauth_scopes')->delete();

        $datetime = Carbon::now();

        $scopes = array(
            array(
                'id' => 'scope1',
                'description' => 'Scope 1 Description',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'id' => 'scope2',
                'description' => 'Scope 2 Description',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_scopes')->insert($scopes);

        DB::table('oauth_client_scopes')->delete();

        $clientScopes = array(
            array(
                'client_id' => 'client1id',
                'scope_id' => 'scope1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'client_id' => 'client2id',
                'scope_id' => 'scope2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_client_scopes')->insert($clientScopes);

        DB::table('oauth_grant_scopes')->delete();

        $grantScopes = array(
            array(
                'grant_id' => 'grant1',
                'scope_id' => 'scope1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'grant_id' => 'grant2',
                'scope_id' => 'scope2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_grant_scopes')->insert($grantScopes);
    }

}
