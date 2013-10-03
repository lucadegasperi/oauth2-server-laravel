<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOAuthSessionTokenScopesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('oauth_session_token_scopes', function(Blueprint $table) {

            $table->increments('id');
            $table->integer('session_access_token_id')->unsigned();
            $table->integer('scope_id')->unsigned();

            $table->timestamps();

            $table->unique('session_access_token_id', 'scope_id');

            $table->index('scope_id');

            $table->foreign('scope_id')
                    ->references('id')->on('oauth_scopes')
                    ->onDelete('cascade')
                    ->onUpdate('no action');

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
		Schema::drop('oauth_session_token_scopes');
	}

}