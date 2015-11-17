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
    Route::get('oauth/authorize', ['as' => 'oauth.authorize.get','middleware' => ['check-authorization-params', 'auth'], function() {
        // display a form where the user can authorize the client to access it's data
       $authParams = Authorizer::getAuthCodeRequestParams();
       $formParams = array_except($authParams,'client');
       $formParams['client_id'] = $authParams['client']->getId();
       return View::make('oauth.authorization-form', ['params'=>$formParams,'client'=>$authParams['client']]);
    }]);
    ```
    **Note:** The form you submit should preserve the query string.  
    **Example of view('oauth.authorization-form'):**
     ```php
    @extends('layouts.default')

    @section('content')
        {!! Form::open(['method' => 'POST','class'=>'form-horizontal', 'url'=> route('oauth.authorize.post',$params)]) !!}
        <div class="form-group">
            <dl class="dl-horizontal">
                <dt>Client Name</dt>
                <dd>{{$client->getName()}}</dd>
            </dl>
        </div>
        {!! Form::hidden('client_id', $params['client_id']) !!}
        {!! Form::hidden('redirect_uri', $params['redirect_uri']) !!}
        {!! Form::hidden('response_type', $params['response_type']) !!}
        {!! Form::hidden('state', $params['state']) !!}
        {!! Form::submit('Approve', ['name'=>'approve', 'value'=>1, 'class'=>'btn btn-success']) !!}
        {!! Form::submit('Deny', ['name'=>'deny', 'value'=>1, 'class'=>'btn bg-danger']) !!}
        {!! Form::close() !!}
    @endsection
     ```

3. Set up a route to respond to the form being posted.

    ```php
    Route::post('oauth/authorize', ['as' => 'oauth.authorize.post','middleware' => ['csrf', 'check-authorization-params', 'auth'], function() {

        $params = Authorizer::getAuthCodeRequestParams();
        $params['user_id'] = Auth::user()->id;
        $redirectUri = '';

        // if the user has allowed the client to access its data, redirect back to the client with an auth code
        if (Input::get('approve') !== null) {
            $redirectUri = Authorizer::issueAuthCode('user', $params['user_id'], $params);
        }

        // if the user has denied the client to access its data, redirect back to the client with an error message
        if (Input::get('deny') !== null) {
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
