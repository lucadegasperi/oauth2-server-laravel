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
        $connection = Config::get('oauth2-server-laravel::oauth2.database');

        Schema::connection($connection)->create('oauth_client_grants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->string('grant_id', 40);
            $table->timestamps();

            $table->index('client_id');
            $table->index('grant_id');

            $table->foreign('client_id')
                  ->references('id')->on('oauth_clients')
                  ->onDelete('cascade')
                  ->onUpdate('no action');

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
        Schema::table('oauth_client_grants', function (Blueprint $table) {
            $table->dropForeign('oauth_client_grants_client_id_foreign');
            $table->dropForeign('oauth_client_grants_grant_id_foreign');
        });
        Schema::drop('oauth_client_grants');
    }
}
