<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthRefreshTokensTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->string('id', 40)->unique();
            $table->string('access_token_id', 40)->primary();
            $table->integer('expire_time');

            $table->timestamps();

            $table->foreign('access_token_id')
                  ->references('id')->on('oauth_access_tokens')
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
        Schema::connection($this->connection)->table('oauth_refresh_tokens', function (Blueprint $table) {
            $table->dropForeign('oauth_refresh_tokens_access_token_id_foreign');
        });

        Schema::connection($this->connection)->drop('oauth_refresh_tokens');
    }
}
