<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Lumen;

use LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider as BaseOAuth2ServerServiceProvider;

/**
 * This is the Lumen oauth server service provider class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class OAuth2ServerServiceProvider extends BaseOAuth2ServerServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // Lumen does not support route filters.
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerConfiguration();
    }

    /**
     * Register the configuration.
     *
     * @return void
     */
    public function registerConfiguration()
    {
        $this->app->configure('oauth2');
    }
}
