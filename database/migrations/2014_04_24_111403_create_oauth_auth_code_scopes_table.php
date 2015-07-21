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
 * This is the create oauth code scopes table migration class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class CreateOauthAuthCodeScopesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('oauth_auth_code_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('auth_code_id', 40);
            $table->string('scope_id', 40);

            $table->timestamps();

            $table->index('auth_code_id');
            $table->index('scope_id');

            $table->foreign('auth_code_id')
                  ->references('id')->on('oauth_auth_codes')
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
        $this->schema()->table('oauth_auth_code_scopes', function (Blueprint $table) {
            $table->dropForeign('oauth_auth_code_scopes_auth_code_id_foreign');
            $table->dropForeign('oauth_auth_code_scopes_scope_id_foreign');
        });
        $this->schema()->drop('oauth_auth_code_scopes');
    }
}
