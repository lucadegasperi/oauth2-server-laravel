<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthClientMetadataTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_client_metadata', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->string('key', 40);
            $table->string('value');
            $table->timestamps();

            $table->foreign('client_id')
                    ->references('id')->on('oauth_clients')
                    ->onDelete('cascade');

            $table->unique(array('client_id', 'key'));
            $table->index('client_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_client_metadata', function ($table) {
            $table->dropForeign('oauth_grant_scopes_client_id_foreign');
        });
        Schema::drop('oauth_client_metadata');
    }
}
