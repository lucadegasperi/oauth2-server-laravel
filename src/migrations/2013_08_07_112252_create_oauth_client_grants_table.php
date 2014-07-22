<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthClientGrantsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dbConnection = Config::get('lucadegasperi/oauth2-server-laravel::oauth2.db_connection') ?: Config::get('database.default');

        Schema::connection($dbConnection)->create('oauth_client_grants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->integer('grant_id')->unsigned();
            $table->timestamps();

            $table->foreign('client_id')
                    ->references('id')->on('oauth_clients')
                    ->onDelete('cascade');

            $table->foreign('grant_id')
                    ->references('id')->on('oauth_grants')
                    ->onDelete('cascade')
                    ->onUpdate('no action');
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

        Schema::connection($dbConnection)->table('oauth_client_grants', function ($table) {
            $table->dropForeign('oauth_client_grants_client_id_foreign');
            $table->dropForeign('oauth_client_grants_grant_id_foreign');
        });
        Schema::connection($dbConnection)->drop('oauth_client_grants');
    }
}
