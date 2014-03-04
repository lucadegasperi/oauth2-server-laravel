<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthGrantScopesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_grant_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grant_id')->unsigned();
            $table->integer('scope_id')->unsigned();

            $table->foreign('grant_id')
                    ->references('id')->on('oauth_grants')
                    ->onDelete('cascade');

            $table->foreign('scope_id')
                    ->references('id')->on('oauth_scopes')
                    ->onDelete('cascade')
                    ->onUpdate('no action');
                    
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
        Schema::table('oauth_grant_scopes', function ($table) {
            $table->dropForeign('oauth_grant_scopes_grant_id_foreign');
            $table->dropForeign('oauth_grant_scopes_scope_id_foreign');
        });
        Schema::drop('oauth_grant_scopes');
    }
}
