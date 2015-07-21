<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use LucaDegasperi\OAuth2Server\Support\AbstractMigration;

/**
 * This is the create oauth sessions table migration class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class CreateOauthSessionsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->enum('owner_type', ['client', 'user'])->default('user');
            $table->string('owner_id');
            $table->string('client_redirect_uri')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'owner_type', 'owner_id']);

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
        $this->schema()->table('oauth_sessions', function (Blueprint $table) {
            $table->dropForeign('oauth_sessions_client_id_foreign');
        });
        $this->schema()->drop('oauth_sessions');
    }
}
