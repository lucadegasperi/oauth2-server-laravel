# Implementing an Authorization Server with the Refresh Token Grant

1. To enable this grant add the following to the `config/oauth2.php` configuration file

  ```php
  'grant_types' => [
      'refresh_token' => [
          'class' => '\League\OAuth2\Server\Grant\RefreshTokenGrant',
          'access_token_ttl' => 3600,
          'refresh_token_ttl' => 36000
      ]
  ]
  ```

  > **Note:** The refresh token grant is to be used together with one other of the following grants: `PasswordGrant`, `AuthCodeGrant`.


2. Set up a route to respond to the incoming access token requests.

  ```php
  Route::post('oauth/access_token', function() {
      return Response::json(Authorizer::issueAccessToken());
  });
  ```

3. Whenever you request an Access Token using a grant that supports the use of the Refresh Token grant, you'll get a Refresh Token together with the Access Token. Once the Access Token expires, use the Refresh Token to require a new one.

---

[&larr; Back to start](../README.md)
