<?php

use Carbon\Carbon;

class ClientsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('oauth_clients')->delete();

        $datetime = Carbon::now();

        $clients = array(
            array(
                'id' => 'client1id',
                'secret' => 'client1secret',
                'name' => 'client1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'id' => 'client2id',
                'secret' => 'client2secret',
                'name' => 'client2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_clients')->insert($clients);

        DB::table('oauth_client_endpoints')->delete();

        $clientEndpoints = array(
            array(
                'client_id' => 'client1id',
                'redirect_uri' => 'http://example1.com/callback',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
            array(
                'client_id' => 'client2id',
                'redirect_uri' => 'http://example2.com/callback',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ),
        );

        DB::table('oauth_client_endpoints')->insert($clientEndpoints);
    }

}
