<?php

// filter to check if the auth code grant type params are provided
Route::filter('check-authorization-params', 'LucaDegasperi\OAuth2Server\Filters\CheckAuthorizationParamsFilter');

// make sure an endpoint is accessible only by authrized members eventually with specific scopes 
Route::filter('oauth', 'LucaDegasperi\OAuth2Server\Filters\OAuthFilter');

// make sure an endpoint is accessible only by a specific owner
Route::filter('oauth-owner', 'LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter');
