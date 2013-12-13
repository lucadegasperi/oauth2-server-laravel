<?php

use Carbon\Carbon;

class ScopesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('oauth_scopes')->delete();

        $datetime = Carbon::now();

        $scopes = array(
            array(
                'scope' => 'scope1',
                'name' => 'scope1',
                'description' => 'Scope 1 Description',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'scope' => 'scope2',
                'name' => 'scope1',
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

        DB::table('oauth_grant_scopes')->insert($grantScopes);
    }

}
