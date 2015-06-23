<?php

namespace LucaDegasperi\OAuth2Server\Lumen;

use LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider as BaseOAuth2ServerServiceProvider;

class OAuth2ServerServiceProvider extends BaseOAuth2ServerServiceProvider
{
    public function boot()
    {
        // Lumen does not support route filters.
    }

    public function register()
    {
        parent::register();
        $this->registerConfiguration();
    }

    public function registerAssets()
    {
        // Lumen cannot publish assets
    }

    public function registerConfiguration()
    {
        $this->app->configure("oauth2");
    }
}
