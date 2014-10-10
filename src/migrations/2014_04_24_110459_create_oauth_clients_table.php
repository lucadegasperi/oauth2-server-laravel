<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthClientsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_clients', function (BluePrint $table) {
            $table->string('id', 40)->primary();
            $table->string('secret', 40);
            $table->string('name');
            $table->timestamps();

            $table->unique(['id', 'secret']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->drop('oauth_clients');
    }
}
