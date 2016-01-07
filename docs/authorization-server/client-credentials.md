# Implementing an Authorization Server with the Client Credentials Grant

1. To enable this grant add the following to the `config/oauth2.php` configuration file:

  ```php
  'grant_types' => [
       'client_credentials' => [
          'class' => '\League\OAuth2\Server\Grant\ClientCredentialsGrant',
          'access_token_ttl' => 3600
      ]
  ]
  ```

2. Next add a couple of `clients` to the `oauth_clients` table.

3. Finally set up a route to respond to the incoming access token requests.

  ```php
  Route::post('oauth/access_token', function() {
      return Response::json(Authorizer::issueAccessToken());
  });
  ```

---

[&larr; Back to start](../README.md)
