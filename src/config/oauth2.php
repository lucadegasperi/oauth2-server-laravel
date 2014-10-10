<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Connection to use
    |--------------------------------------------------------------------------
    |
    | Set the default database connection to use for the repositories,
    | when set to default, it uses whatever connection you specified in your laravel db config.
    |
    */
    'database' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Supported Grant Types
    |--------------------------------------------------------------------------
    |
    | Your OAuth2 Server can issue an access token based on different grant types
    | you can even provide your own grant type.
    | To choose which grant type suits your scenario, see
    | https://github.com/php-loep/oauth2-server/wiki/Which-OAuth-2.0-grant-should-I-use%3F
    |
    | Available grant types are:
    |
    | 'grant_types' => [
    |
    |    'authorization_code' => [
    |        'class'            => 'League\OAuth2\Server\Grant\AuthCodeGrant',
    |        'access_token_ttl' => 3600,
    |
    |        // the authorization code time to live
    |        'auth_token_ttl'   => 3600,
    |    ],
    |
    |    'password' => [
    |        'class'            => 'League\OAuth2\Server\Grant\PasswordGrant',
    |        'access_token_ttl' => 604800,
    |
    |        // the code to run in order to verify the user's identity
    |        'callback'         => function($username, $password){
    |            $credentials = [
    |                'email'    => $username,
    |                'password' => $password,
    |            ];
    |
    |            if (Auth::once($credentials)) {
    |                return Auth::user()->id;
    |            } else {
    |                return false;
    |            }
    |        }
    |    ],
    |
    |    'client_credentials' => [
    |        'class'                 => 'League\OAuth2\Server\Grant\ClientCredentialsGrant',
    |        'access_token_ttl'      => 3600,
    |    ],
    |
    |    'refresh_token' => [
    |        'class'                 => 'League\OAuth2\Server\Grant\RefreshTokenGrant',
    |        'access_token_ttl'      => 3600,
    |
    |        // the refresh token time to live
    |        'refresh_token_ttl'     => 604800,
    |
    |        // whether or not to issue a new refresh token when a new access token is issued
    |        'rotate_refresh_tokens' => false,
    |    ],
    |
    | ],
    |
    */

    'grant_types' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Output Token Type
    |--------------------------------------------------------------------------
    |
    | This will tell the authorization server the output format for the access token
    | and will tell the resource server how to parse the access token used.
    |
    | Default value is League\OAuth2\Server\TokenType\Bearer
    |
    */
    'token_type' => 'League\OAuth2\Server\TokenType\Bearer',

    /*
    |--------------------------------------------------------------------------
    | State Parameter
    |--------------------------------------------------------------------------
    |
    | Whether or not the state parameter is required in the query string
    |
    */
    'state_param' => false,

    /*
    |--------------------------------------------------------------------------
    | Scope Parameter
    |--------------------------------------------------------------------------
    |
    | Whether or not the scope parameter is required in the query string
    |
    */
    'scope_param' => false,

    /*
    |--------------------------------------------------------------------------
    | Scope Delimiter
    |--------------------------------------------------------------------------
    |
    | Which character to use to split the scope parameter in the query string
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
    'default_scope' => null,

    /*
    |--------------------------------------------------------------------------
    | Access Token TTL
    |--------------------------------------------------------------------------
    |
    | For how long the issued access token is valid (in seconds)
    | this can be also set on a per grant-type basis
    |
    */
    'access_token_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Limit clients to specific grants
    |--------------------------------------------------------------------------
    |
    | Whether or not to limit clients to specific grant types
    | This is useful to allow only trusted clients to access your API differently
    |
    */
    'limit_clients_to_grants' => false,

    /*
    |--------------------------------------------------------------------------
    | Limit clients to specific scopes
    |--------------------------------------------------------------------------
    |
    | Whether or not to limit clients to specific scopes
    | This is useful to only allow specific clients to use some scopes
    |
    */
    'limit_clients_to_scopes' => false,

    /*
    |--------------------------------------------------------------------------
    | Limit scopes to specific grants
    |--------------------------------------------------------------------------
    |
    | Whether or not to limit scopes to specific grants
    | This is useful to allow certain scopes to be used only with certain grant types
    |
    */
    'limit_scopes_to_grants' => false,

    /*
    |--------------------------------------------------------------------------
    | HTTP Header Only
    |--------------------------------------------------------------------------
    |
    | This will tell the resource server where to check for the access_token.
    | By default it checks both the query string and the http headers
    |
    */
    'http_headers_only' => false,
];
