<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthSessionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->enum('owner_type', array('client', 'user'))->default('user');
            $table->string('owner_id');
            $table->timestamps();

            $table->index(array('client_id', 'owner_type', 'owner_id'));

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
        Schema::table('oauth_sessions', function ($table) {
            $table->dropForeign('oauth_sessions_client_id_foreign');
        });
        
        Schema::drop('oauth_sessions');
    }
}
