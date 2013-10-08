<?php namespace LucaDegasperi\OAuth2Server\Proxies;

use League\OAuth2\Server\Authorization as Authorization;
use League\OAuth2\Server\Util\RedirectUri;
use League\OAuth2\Server\Exception\ClientException;
use Exception;
use Response;

class AuthorizationServerProxy {

    protected $authServer;

    public function __construct(Authorization $authServer)
    {
        $this->authServer = $authServer;
    }

    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->authServer->$method();
            case 1:
                return $this->authServer->$method($args[0]);
            case 2:
                return $this->authServer->$method($args[0], $args[1]);
            case 3:
                return $this->authServer->$method($args[0], $args[1], $args[2]);
            case 4:
                return $this->authServer->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($this->authServer, $method), $args);
        }
    }

    public function makeRedirect($uri, $params = array(), $queryDelimeter = '?')
    {
        return RedirectUri::make($uri, $params, $queryDelimeter);
    }

    public function checkAuthorizeParams()
    {
        return $this->authServer->getGrantType('authorization_code')->checkAuthoriseParams();
    }

    public function performAccessTokenFlow()
    {
        try {

            // Tell the auth server to issue an access token
            $response = $this->authServer->issueAccessToken();

        } catch (ClientException $e) {

            // Throw an exception because there was a problem with the client's request
            $response = array(
                'error' =>  $this->authServer->getExceptionType($e->getCode()),
                'error_description' => $e->getMessage()
            );

            // make this better in order to return the correct headers via the response object
            $headers = $this->authServer->getExceptionHttpHeaders($this->authServer->getExceptionType($e->getCode()));
            foreach ($headers as $header) {
                header($header);
            }

        } catch (Exception $e) {

            // Throw an error when a non-library specific exception has been thrown
            $response = array(
                'error' =>  'undefined_error',
                'error_description' => $e->getMessage()
            );

            return Response::json($response, 500);
        }

        return Response::json($response);
    }

}