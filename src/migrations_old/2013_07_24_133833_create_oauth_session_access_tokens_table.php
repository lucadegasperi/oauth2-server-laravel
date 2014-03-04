<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthSessionAccessTokensTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_session_access_tokens', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('session_id')->unsigned();
            $table->string('access_token', 40);
            $table->integer('access_token_expires');

            $table->timestamps();

            $table->unique(array('access_token', 'session_id'));
            $table->index('session_id');

            $table->foreign('session_id')
                    ->references('id')->on('oauth_sessions')
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
        Schema::table('oauth_session_access_tokens', function ($table) {
            $table->dropForeign('oauth_session_access_tokens_session_id_foreign');
        });
        Schema::drop('oauth_session_access_tokens');
    }
}
