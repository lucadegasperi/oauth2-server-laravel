<?php
/**
 * Auth Code Checker Delegate for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Delegates;


use League\OAuth2\Server\Exception\OAuthException;

interface AuthCodeCheckerDelegate {

    public function checkSuccessful();

    public function checkFailed(OAuthException $e);
} 