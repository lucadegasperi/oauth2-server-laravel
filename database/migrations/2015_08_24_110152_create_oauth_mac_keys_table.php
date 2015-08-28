<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This is the create oauth mac keys table migration class.
 *
 */
class CreateOauthMacKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_mac_keys', function (Blueprint $table) {
            $table->increments('id');

            $table->string('access_token_id', 40)->index();
            $table->string('mac_key');

            $table->foreign('access_token_id')
                  ->references('id')->on('oauth_access_tokens')
                  ->onDelete('cascade');
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
        Schema::table('oauth_mac_keys', function (Blueprint $table) {
            $table->dropForeign('oauth_mac_keys_access_token_id_foreign');
        });
        Schema::drop('oauth_mac_keys');
    }
}