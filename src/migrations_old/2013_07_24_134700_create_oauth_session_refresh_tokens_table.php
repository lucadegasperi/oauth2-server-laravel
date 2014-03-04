<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthSessionRefreshTokensTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_session_refresh_tokens', function (Blueprint $table) {

            $table->integer('session_access_token_id')->unsigned()->primary();
            $table->string('refresh_token', 40);
            $table->integer('refresh_token_expires');
            $table->string('client_id', 40);

            $table->timestamps();

            $table->index('client_id');

            $table->foreign('client_id')
                    ->references('id')->on('oauth_clients')
                    ->onDelete('cascade');

            $table->foreign('session_access_token_id')
                    ->references('id')->on('oauth_session_access_tokens')
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
        Schema::table('oauth_session_refresh_tokens', function ($table) {
            $table->dropForeign('oauth_session_refresh_tokens_client_id_foreign');
            $table->dropForeign('oauth_session_refresh_tokens_session_access_token_id_foreign');
        });
        Schema::drop('oauth_session_refresh_tokens');
    }
}
