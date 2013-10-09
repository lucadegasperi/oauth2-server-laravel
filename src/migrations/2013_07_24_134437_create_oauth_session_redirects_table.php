<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOAuthSessionRedirectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_session_redirects', function (Blueprint $table) {

            $table->integer('session_id')->unsigned();
            $table->string('redirect_uri');

            $table->timestamps();

            $table->primary('session_id');

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
        Schema::drop('oauth_session_redirects');
    }
}
