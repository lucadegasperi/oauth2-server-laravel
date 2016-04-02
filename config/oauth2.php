<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Grant Types
    |--------------------------------------------------------------------------
    |
    | Your OAuth2 Server can issue an access token based on different grant
    | types you can even provide your own grant type.
    |
    | To choose which grant type suits your scenario, see
    | http://oauth2.thephpleague.com/authorization-server/which-grant
    |
    | Please see this link to find available grant types
    | http://git.io/vJLAv
    |
    */
    'grant_types' => [
        [
            'class' => \League\OAuth2\Server\Grant\PasswordGrant::class,
            'access_token_ttl' => '',
        ]
    ],


    /*
    |--------------------------------------------------------------------------
    | Private Key Path
    |--------------------------------------------------------------------------
    |
    | This will tell the authorization server the output format for the access
    | token and the resource server how to parse the access token used.
    |
    */
    'private_key_path' => 'file://path_to_private_key/private.key',


    /*
    |--------------------------------------------------------------------------
    | Public Key Path
    |--------------------------------------------------------------------------
    |
    | This will tell the authorization server the output format for the access
    | token and the resource server how to parse the access token used.
    |
    */
    'public_key_path' => 'file://path_to_private_key/public.key',

    /*
    |--------------------------------------------------------------------------
    | Output Response Type
    |--------------------------------------------------------------------------
    |
    | This will tell the authorization server the output format for the access
    | token and the resource server how to parse the access token used.
    |
    | Default value is \League\OAuth2\Server\ResponseTypes\BearerTokenResponse
    |
    */
    'response_type' => \League\OAuth2\Server\ResponseTypes\BearerTokenResponse::class,


    /*
    |--------------------------------------------------------------------------
    | Authorization Validator
    |--------------------------------------------------------------------------
    |
    | This will tell the resource server the validator to use to validate an incoming request
    |
    | Default value is \League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator::class
    |
    */
    'authorization_validator' => \League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator::class

];