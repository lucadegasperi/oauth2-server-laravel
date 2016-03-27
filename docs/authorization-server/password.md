# Implementing an Authorization Server with the Password Grant

1. To enable this grant add the following to the `config/oauth2.php` configuration file.
    ```php
    'grant_types' => [
        'password' => [
            'class' => '\League\OAuth2\Server\Grant\PasswordGrant',
            'callback' => '\App\PasswordGrantVerifier@verify',
            'access_token_ttl' => 3600
        ]
    ]
    ```

2. Create a class with a `verify` method where you check if the provided user is a valid one. In the following example you have to create a `PasswordGrantVerifier.php` in your `app` folder.

    ```php
    namespace App;
    
    use Illuminate\Support\Facades\Auth;

    class PasswordGrantVerifier
    {
      public function verify($username, $password)
      {
          $credentials = [
            'email'    => $username,
            'password' => $password,
          ];

          if (Auth::once($credentials)) {
              return Auth::user()->id;
          }

          return false;
      }
    }
    ```

3. Next add a sample `client` to the `oauth_clients` table.  

4. Finally set up a route to respond to the incoming access token requests.

    ```php
    Route::post('oauth/access_token', function() {
        return Response::json(Authorizer::issueAccessToken());
    });
    ```

---

[&larr; Back to start](../README.md)
