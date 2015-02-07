<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthAuthCodeScopesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_auth_code_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('auth_code_id', 40);
            $table->string('scope_id', 40);

            $table->timestamps();

            $table->index('auth_code_id');
            $table->index('scope_id');

            $table->foreign('auth_code_id')
                  ->references('id')->on('oauth_auth_codes')
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
        $this->schema()->table('oauth_auth_code_scopes', function (Blueprint $table) {
            $table->dropForeign('oauth_auth_code_scopes_auth_code_id_foreign');
            $table->dropForeign('oauth_auth_code_scopes_scope_id_foreign');
        });
        $this->schema()->drop('oauth_auth_code_scopes');
    }
}
