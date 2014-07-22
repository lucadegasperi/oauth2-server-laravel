<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthClientScopesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dbConnection = Config::get('lucadegasperi/oauth2-server-laravel::oauth2.db_connection') ?: Config::get('database.default');

        Schema::connection($dbConnection)->create('oauth_client_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->integer('scope_id')->unsigned();

            $table->foreign('client_id')
                    ->references('id')->on('oauth_clients')
                    ->onDelete('cascade');

            $table->foreign('scope_id')
                    ->references('id')->on('oauth_scopes')
                    ->onDelete('cascade')
                    ->onUpdate('no action');
            $table->timestamps();
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

        Schema::connection($dbConnection)->table('oauth_client_scopes', function ($table) {
            $table->dropForeign('oauth_client_scopes_client_id_foreign');
            $table->dropForeign('oauth_client_scopes_scope_id_foreign');
        });
        Schema::connection($dbConnection)->drop('oauth_client_scopes');
    }
}
