# Securing your API endpoints

This package comes with a series of tools to help you protect your API endpoints using OAuth 2.0. This package include the access token verification and the permissions verification. First let's talk about defining permissions (scopes).

### Defining scopes

In the context of OAuth, scopes are the part of your API, the client (the third-party application) is trying to access. You can think of them as a sort of permission the client asks for. Scopes are a completely arbitrary string defined by you. When using this package, all your scopes should be saved into the `oauth_scopes` table.

When a client asks for an access token he'll also provide the scopes he needs for the application. The authorization server will then verify the scopes exist and the client has the right to use them.

> Using scopes is optional, but any non trivial application will benefit from them.

### Checking the access token

When requesting the resources of a protected endpoint the client should send an access token (previously issued to it) and the endpoint should check its validity. This is achieved by using the `oauth` middleware on any route of your API you want to protect with OAuth.

```php
Route::get('protected-resource', ['middleware' => 'oauth', function() {
    // return the protected resource
}]);
```

This middleware will allow the access to the protected resource to any client with a valid access token. It will also send the client an error if he hasn't provided a valid access token. If you want to limit the access to the resource only to clients with certain permissions, here's where scopes come in handy.

### Checking the scopes

Every access token is tied to the client, the resource owner and the scopes it can access. To check if the client can access a resource with its permission, use the `oauth` middleware with the optional arguments. This will check the validity of the access token and the permissions.

```php
Route::get('protected-resource', ['middleware' => 'oauth:scope1+scope2', function() {
    // return the protected resource
}]);
```

When at least one of the scope doesn't match the permissions the client has, the middleware will return an error to the client, informing it that it doesn't have the required permissions to access the endpoint.

### Checking the access token owner

When using the client_credentials grant type, the access token owner and the client can be the same entity to distinguish this particular case there's the `oauth-user` middleware. Parameters for this middleware are either `client` or `user`

### Finding access token owner

```php
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

Authorizer::getResourceOwnerId();
```

---

[&larr; Back to start](../README.md)
