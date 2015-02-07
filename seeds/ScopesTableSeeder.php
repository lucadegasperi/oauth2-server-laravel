<?php
/**
 * Scopes Table Seeder
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScopesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('oauth_scopes')->delete();

        $datetime = Carbon::now();

        $scopes = [
            [
                'id' => 'scope1',
                'description' => 'Scope 1 Description',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'id' => 'scope2',
                'description' => 'Scope 2 Description',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_scopes')->insert($scopes);

        DB::table('oauth_client_scopes')->delete();

        $clientScopes = [
            [
                'client_id' => 'client1id',
                'scope_id' => 'scope1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'client_id' => 'client2id',
                'scope_id' => 'scope2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_client_scopes')->insert($clientScopes);

        DB::table('oauth_grant_scopes')->delete();

        $grantScopes = [
            [
                'grant_id' => 'grant1',
                'scope_id' => 'scope1',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
            [
                'grant_id' => 'grant2',
                'scope_id' => 'scope2',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ],
        ];

        DB::table('oauth_grant_scopes')->insert($grantScopes);
    }
}
