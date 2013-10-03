<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOAuthClientEndpointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('oauth_client_endpoints', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->string('redirect_uri');

            $table->timestamps();

            $table->foreign('client_id')
                    ->references('id')->on('oauth_clients')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('oauth_client_endpoints');
	}

}