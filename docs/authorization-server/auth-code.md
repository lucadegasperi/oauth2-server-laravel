# Implementing an Authorization Server with the Auth Code Grant

1. To enable this grant add the following to the `config/oauth2.php` configuration file

    ```php
    'grant_types' => [
        'authorization_code' => [
            'class' => '\League\OAuth2\Server\Grant\AuthCodeGrant',
            'access_token_ttl' => 3600,
            'auth_token_ttl'   => 3600
        ]
    ]
    ```

2. Set up a route to respond to the incoming auth code requests

    ```php
    Route::get('oauth/authorize', ['as' => 'oauth.authorize.get', 'middleware' => ['check-authorization-params', 'auth'], function() {
       $authParams = Authorizer::getAuthCodeRequestParams();

       $formParams = array_except($authParams,'client');

       $formParams['client_id'] = $authParams['client']->getId();

       $formParams['scope'] = implode(config('oauth2.scope_delimiter'), array_map(function ($scope) {
           return $scope->getId();
       }, $authParams['scopes']));

       return View::make('oauth.authorization-form', ['params' => $formParams, 'client' => $authParams['client']]);
    }]);
    ```
    > **Note:** The form you submit should preserve the query string.  

    ```php
    <h2>{{$client->getName()}}</h2>
    <form method="post" action="{{route('oauth.authorize.post', $params)}}">
      {{ csrf_field() }}
      <input type="hidden" name="client_id" value="{{$params['client_id']}}">
      <input type="hidden" name="redirect_uri" value="{{$params['redirect_uri']}}">
      <input type="hidden" name="response_type" value="{{$params['response_type']}}">
      <input type="hidden" name="state" value="{{$params['state']}}">
      <input type="hidden" name="scope" value="{{$params['scope']}}">

      <button type="submit" name="approve" value="1">Approve</button>
      <button type="submit" name="deny" value="1">Deny</button>
    </form>
    ```

3. Set up a route to respond to the form being posted.

    ```php
    Route::post('oauth/authorize', ['as' => 'oauth.authorize.post', 'middleware' => ['csrf', 'check-authorization-params', 'auth'], function() {

        $params = Authorizer::getAuthCodeRequestParams();
        $params['user_id'] = Auth::user()->id;
        $redirectUri = '/';

        // If the user has allowed the client to access its data, redirect back to the client with an auth code.
        if (Request::has('approve')) {
            $redirectUri = Authorizer::issueAuthCode('user', $params['user_id'], $params);
        }

        // If the user has denied the client to access its data, redirect back to the client with an error message.
        if (Request::has('deny')) {
            $redirectUri = Authorizer::authCodeRequestDeniedRedirectUri();
        }

        return Redirect::to($redirectUri);
    }]);
    ```

4. Add a route to respond to the access token requests

    ```php
    Route::post('oauth/access_token', function() {
        return Response::json(Authorizer::issueAccessToken());
    });
    ```

5. Next add a sample `client` to the `oauth_clients` table.  

6. And finally add `redirect_uri` to the `oauth_client_endpoints` table for `client`.

---

[&larr; Back to start](../README.md)
