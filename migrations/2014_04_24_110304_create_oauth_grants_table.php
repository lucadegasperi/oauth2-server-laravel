<?php

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\AbstractMigration;

class CreateOauthGrantsTable extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_grants', function (Blueprint $table) {
            $table->string('id', 40)->primary();
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
        $this->schema()->drop('oauth_grants');
    }
}
