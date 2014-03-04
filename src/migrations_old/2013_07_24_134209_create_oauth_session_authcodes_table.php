<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthSessionAuthcodesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_session_authcodes', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('session_id')->unsigned();
            $table->string('auth_code', 40);
            $table->integer('auth_code_expires');

            $table->timestamps();

            $table->index('session_id');

            $table->foreign('session_id')
                    ->references('id')->on('oauth_sessions')
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
        Schema::table('oauth_session_authcodes', function ($table) {
            $table->dropForeign('oauth_session_authcodes_session_id_foreign');
        });
        Schema::drop('oauth_session_authcodes');
    }
}
