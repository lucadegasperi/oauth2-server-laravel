# Middlewares

This package comes with four different middlewares to make the integration into Laravel much easier.

### OAuthMiddleware

This is the core middleware which should be used in almost all cases for authoring requests.

### OAuthClientOwnerMiddleware

Use this middleware to check if the current authorization request owner is of the type `client`. This middleware is associated with the `client_credentials` grant.

### OAuthUserOwnerMiddleware

Use this middleware to check if the current authorization request owner is of the type `user`. This middleware is associated with the `password` grant.

> **Note:** this middleware is required in order to fetch the current resource owners ID.

### CheckAuthCodeRequestMiddleware

Use this middleware to check access tokens on the client after successfully authenticating the resource owner and obtaining authorization. This middleware is associated with the `authorization_code` grant.

## Order

Please note that the middlewares has to be applied in a certain order. The *OAuthMiddleware* has to be added before the `OAuthClientOwnerMiddleware` and the `OAuthUserOwnerMiddleware`.

```php
public function __construct()
{
    $this->middleware('oauth');
    $this->middleware('oauth-user');
}
```

If the middlewares isn't in the correct order, methods like the `Authorizer::getResourceOwnerId()` wont work.

---

[&larr; Back to start](../README.md)
