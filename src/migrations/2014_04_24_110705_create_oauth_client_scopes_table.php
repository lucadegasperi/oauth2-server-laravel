<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\Migration;

class CreateOauthClientScopesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_client_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->string('scope_id', 40);

            $table->timestamps();

            $table->index('client_id');
            $table->index('scope_id');

            $table->foreign('client_id')
                  ->references('id')->on('oauth_clients')
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
        $this->schema()->table('oauth_client_scopes', function (Blueprint $table) {
            $table->dropForeign('oauth_client_scopes_client_id_foreign');
            $table->dropForeign('oauth_client_scopes_scope_id_foreign');
        });
        $this->schema()->drop('oauth_client_scopes');
    }
}
