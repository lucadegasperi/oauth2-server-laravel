<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthSessionAuthcodeScopesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dbConnection = Config::get('lucadegasperi/oauth2-server-laravel::oauth2.db_connection') ?: Config::get('database.default');

        Schema::connection($dbConnection)->create('oauth_session_authcode_scopes', function (Blueprint $table) {

            $table->integer('oauth_session_authcode_id')->unsigned();
            $table->integer('scope_id')->unsigned();

            $table->timestamps();

            $table->index('oauth_session_authcode_id');
            $table->index('scope_id');

            $table->foreign('scope_id')
                    ->references('id')->on('oauth_scopes')
                    ->onDelete('cascade');

            $table->foreign('oauth_session_authcode_id')
                    ->references('id')->on('oauth_session_authcodes')
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
        $dbConnection = Config::get('lucadegasperi/oauth2-server-laravel::oauth2.db_connection') ?: Config::get('database.default');

        Schema::connection($dbConnection)->table('oauth_session_authcode_scopes', function ($table) {
            $table->dropForeign('oauth_session_authcode_scopes_scope_id_foreign');
            $table->dropForeign('oauth_session_authcode_scopes_oauth_session_authcode_id_foreign');
        });
        Schema::connection($dbConnection)->drop('oauth_session_authcode_scopes');
    }
}
