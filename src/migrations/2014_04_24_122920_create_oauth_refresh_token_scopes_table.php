<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthRefreshTokenScopesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_refresh_token_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('refresh_token_id', 40);
            $table->string('scope_id', 40);

            $table->timestamps();

            $table->index('refresh_token_id');
            $table->index('scope_id');

            $table->foreign('refresh_token_id')
                ->references('id')->on('oauth_refresh_tokens')
                ->onDelete('cascade');

            $table->foreign('scope_id')
                ->references('id')->on('oauth_scopes')
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
        Schema::table('oauth_refresh_token_scopes', function (Blueprint $table) {
            $table->dropForeign('oauth_refresh_token_scopes_scope_id_foreign');
            $table->dropForeign('oauth_refresh_token_scopes_refresh_token_id_foreign');
        });
        Schema::drop('oauth_refresh_token_scopes');
    }
}
