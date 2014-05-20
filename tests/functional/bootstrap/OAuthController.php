<?php

use Illuminate\Routing\Controller;
use League\OAuth2\Server\Exception\OAuthException;
use LucaDegasperi\OAuth2Server\Delegates\AccessTokenIssuerDelegate;
use LucaDegasperi\OAuth2Server\Authorizer;

class OAuthController extends Controller implements AccessTokenIssuerDelegate
{
    protected $authorizer;

    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;

        $this->beforeFilter('auth', ['only' => ['getAuthorize', 'postAuthorize']]);
        $this->beforeFilter('csrf', ['only' => 'postAuthorize']);
        $this->beforeFilter('check-authorization-params', ['only' => ['getAuthorize', 'postAuthorize']]);
    }

    public function postAccessToken()
    {
        return $this->authorizer->issueAccessToken($this);
    }

    public function accessTokenIssued($responseMessage)
    {
        return Response::json($responseMessage);
    }

    public function accessTokenIssuingFailed(OAuthException $e)
    {
        return Response::json(
            [
                'error' => $e->errorType,
                'error_message' => $e->getMessage()
            ],
            $e->httpStatusCode,
            $e->getHttpHeaders()
        );
    }

    public function getAuthorize()
    {
        return View::make('authorization-form', $this->authorizer->getAuthCodeRequestParams());
    }

    public function postAuthorize()
    {
        // get the user id
        $params['user_id'] = Auth::user()->id;

        // check if the user approved or denied the authorization request
        if (Input::get('approve') !== null) {
            $redirectUrl = $this->authorizer->issueAuthCode('user', $params['user_id'], $params);
            return Redirect::to($redirectUrl);
        }

        if (Input::get('deny') !== null) {
            //return Redirect::to(AuthorizationServer::makeRedirectWithError($params));
        }
    }
}
