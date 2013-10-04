<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Supported Grant Types
    |--------------------------------------------------------------------------
    |
    | Your OAuth2 Server can issue an access token based on different grant types
    | you can even provide your own grant type.
    | Available grant types are:
    | 
    | League\OAuth2\Server\Grant\AuthCode
    | League\OAuth2\Server\Grant\Password
    | League\OAuth2\Server\Grant\RefreshToken
    | League\OAuth2\Server\Grant\ClientCredentials
    | League\OAuth2\Server\Grant\Implicit
    |
    */
    'grant_types' => array(

        'authorization_code' => array(
            'class'            => 'League\OAuth2\Server\Grant\AuthCode',
            'access_token_ttl' => 3600,
        ),

        'password' => array(
            'class'            => 'League\OAuth2\Server\Grant\Password',
            'access_token_ttl' => 3600,
        ),

        'refresh_token' => array(
            'class'            => 'League\OAuth2\Server\Grant\RefreshToken',
            'access_token_ttl' => 3600,
        ),
        
    ),

    /*
    |--------------------------------------------------------------------------
    | State Parameter
    |--------------------------------------------------------------------------
    |
    | Whether or not the state parameter is required in the query string
    |
    */
    'state_param' => true,

    /*
    |--------------------------------------------------------------------------
    | Scope Parameter
    |--------------------------------------------------------------------------
    |
    | Whether or not the scope parameter is required in the query string
    |
    */
    'scope_param' => true,

    /*
    |--------------------------------------------------------------------------
    | Scope Delimiter
    |--------------------------------------------------------------------------
    |
    | Which caracter to use to split the scope parameter in the query string
    |
    */
    'scope_delimiter' => ',',

    /*
    |--------------------------------------------------------------------------
    | Default Scope
    |--------------------------------------------------------------------------
    |
    | The default scope to use if not present in the query string
    |
    */
    'default_scope' => 'basic',

    /*
    |--------------------------------------------------------------------------
    | Access Token TTL
    |--------------------------------------------------------------------------
    |
    | For how long the issued access token is valid (in seconds)
    |
    */
    'access_token_ttl' => 3600,


    'limit_clients_to_grants' => false,

    'limit_clients_to_scopes' => false,

    'limit_scopes_to_grants' => false,

    'http_headers_only' => false,
);