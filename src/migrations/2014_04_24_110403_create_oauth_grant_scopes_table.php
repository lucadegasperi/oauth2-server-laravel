<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthGrantScopesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_grant_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('grant_id', 40);
            $table->string('scope_id', 40);

            $table->timestamps();

            $table->index('grant_id');
            $table->index('scope_id');

            $table->foreign('grant_id')
                ->references('id')->on('oauth_grants')
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
        $this->schema()->table('oauth_grant_scopes', function (Blueprint $table) {
            $table->dropForeign('oauth_grant_scopes_grant_id_foreign');
            $table->dropForeign('oauth_grant_scopes_scope_id_foreign');
        });
        $this->schema()->drop('oauth_grant_scopes');
    }
}
