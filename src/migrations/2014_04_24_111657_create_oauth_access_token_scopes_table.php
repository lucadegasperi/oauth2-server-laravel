<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthAccessTokenScopesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = Config::get('oauth2-server-laravel::oauth2.database');

        Schema::connection($connection)->create('oauth_access_token_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('access_token_id', 40);
            $table->string('scope_id', 40);

            $table->timestamps();

            $table->index('access_token_id');
            $table->index('scope_id');

            $table->foreign('access_token_id')
                  ->references('id')->on('oauth_access_tokens')
                  ->onDelete('cascade');

            $table->foreign('scope_id')
                  ->references('id')->on('oauth_scopes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_access_token_scopes', function (Blueprint $table) {
            $table->dropForeign('oauth_access_token_scopes_scope_id_foreign');
            $table->dropForeign('oauth_access_token_scopes_access_token_id_foreign');
        });
        Schema::drop('oauth_access_token_scopes');
    }
}
