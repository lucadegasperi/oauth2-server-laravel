# Config

The file in `config/oauth2.php` contains plenty of options you can use to configure your OAuth 2.0 server implementation to suit the needs of your business. Here's the explanation for them.

### `grant_types`
options: `array`

An array of grant types supported by the authorization server to obtain an access token. See [Issuing access tokens](https://github.com/lucadegasperi/oauth2-server-laravel/blob/master/docs/authorization-server/choosing-grant.md) for how to configure and use the different grant types.

### `token_type`
options: `string`

default: `League\OAuth2\Server\TokenType\Bearer`

This option informs the authorization server how the returned tokens should be formatted.

### `state_param`
options: `true` or `false`

default: `false`

If this option is true, each request to the authorization server should contain a `&state=random_string` param. The state parameter is an additional security measure and the authorization server will reply back to your request with a response containing the same state param you passed. If the state param between the request and response doesn't match, the authorization server might have been compromised.

### `scope_param`
options: `true` or `false`

default: `false`

Whether or not the scope parameter is required in the query string. The scope(s) reflects the type of permission the client wants to access on user's behalf. See [Defining Scopes] for how to use scopes.


### `scope_delimiter`
options: `string`

default `','`

The separator used to split the different scopes provided in the query string when multiple scopes are provided. See [Defining Scopes] for how to use scopes.


### `default_scope`
options: `string` or `null`

default: `null`

This option indicates the default scope each access token request has when no scope parameter is provided in the query string. `null` means the requests have no default scope. See [Defining Scopes] for how to use scopes.


### `access_token_ttl`
options: `integer`

default: `3600`

The number of seconds after an issued access token is not considered valid. Can be overwritten on a grant type basis.

### `limit_clients_to_grants`
options: `true` or `false`

default: `false`

This options sets whether or not a client is limited to specific grant types for obtaining an access token. The `oauth_client_grants` table regulates which clients can use which grant types.

### `limit_clients_to_scopes`
options: `true` or `false`

default: `false`

This options sets which clients can use which scopes. It is useful for allowing different grades of permissions to different clients. The `oauth_client_scopes` table regulates which clients can use which scopes.

### `limit_scopes_to_grants`
options: `true` or `false`

default: `false`

This options allows the use of certain scopes only when required with the appropriate grant type. this is due to the fact that different grant types have different grades of security. For example, a highly permissive scope should be allowed only to clients you trust or can request an access token securely.
The `oauth_grant_scopes` table regulates which grant types can use which scopes.

### `http_headers_only`
options: `true` or `false`

default: `false`

This options tells the resource server where to check for the access token. If set to true only the http headers will be checked.

---

[&larr; Back to start](../README.md)
