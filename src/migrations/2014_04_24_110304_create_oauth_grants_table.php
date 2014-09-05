<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthGrantsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create('oauth_grants', function (Blueprint $table) {
            $table->string('id', 40)->primary();
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
        Schema::connection($this->connection)->drop('oauth_grants');
    }
}
