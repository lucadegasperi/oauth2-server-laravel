<?php namespace LucaDegasperi\OAuth2Server\Facades;

use Illuminate\Support\Facades\Facade;
use League\OAuth2\Server\Util\RedirectUri;
use League\OAuth2\Server\Exception\ClientException;
use Response;

class AuthorizationServerFacade extends Facade {

    /**
     * @codeCoverageIgnore
     */
    protected static function getFacadeAccessor() { return 'oauth2.authorization-server'; }

    public static function makeRedirect($uri, $params = array(), $queryDelimeter = '?')
    {
        return RedirectUri::make($uri, $params, $queryDelimeter);
    }

    public static function performAccessTokenFlow()
    {
        try {

            // Tell the auth server to issue an access token
            $response = self::issueAccessToken();

        } catch (ClientException $e) {

            // Throw an exception because there was a problem with the client's request
            $response = array(
                'error' =>  self::getExceptionType($e->getCode()),
                'error_description' => $e->getMessage()
            );

            $headers = self::getExceptionHttpHeaders(self::getExceptionType($e->getCode()));
            foreach ($headers as $header) {
                header($header);
            }

        } catch (Exception $e) {

            // Throw an error when a non-library specific exception has been thrown
            $response = array(
                'error' =>  'undefined_error',
                'error_description' => $e->getMessage()
            );
        }

        return Response::json($response);
    }
}


