<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthAuthCodesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_auth_codes', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->integer('session_id')->unsigned();
            $table->string('redirect_uri');
            $table->integer('expire_time');

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
        $this->schema()->table('oauth_auth_codes', function (Blueprint $table) {
            $table->dropForeign('oauth_auth_codes_session_id_foreign');
        });
        $this->schema()->drop('oauth_auth_codes');
    }
}
