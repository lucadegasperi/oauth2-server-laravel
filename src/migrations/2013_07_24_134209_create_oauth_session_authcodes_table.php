<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthSessionAuthcodesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dbConnection = Config::get('lucadegasperi/oauth2-server-laravel::oauth2.db_connection') ?: Config::get('database.default');

        Schema::connection($dbConnection)->create('oauth_session_authcodes', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('session_id')->unsigned();
            $table->string('auth_code', 40);
            $table->integer('auth_code_expires');

            $table->timestamps();

            $table->index('session_id');

            $table->foreign('session_id')
                    ->references('id')->on('oauth_sessions')
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

        Schema::connection($dbConnection)->table('oauth_session_authcodes', function ($table) {
            $table->dropForeign('oauth_session_authcodes_session_id_foreign');
        });
        Schema::connection($dbConnection)->drop('oauth_session_authcodes');
    }
}
