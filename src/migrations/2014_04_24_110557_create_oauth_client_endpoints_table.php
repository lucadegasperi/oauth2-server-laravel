<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthClientEndpointsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_client_endpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->string('redirect_uri');

            $table->timestamps();

            $table->unique(['client_id', 'redirect_uri']);

            $table->foreign('client_id')
                ->references('id')->on('oauth_clients')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('oauth_client_endpoints', function (Blueprint $table) {
            $table->dropForeign('oauth_client_endpoints_client_id_foreign');
        });

        $this->schema()->drop('oauth_client_endpoints');
    }
}
