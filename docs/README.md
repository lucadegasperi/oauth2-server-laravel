# Documentation

This wiki will guide you through all the things you need to successfully integrate an OAuth 2.0 compliant server into your Laravel applications. Let's begin!

## Getting Started

1. [Introduction](getting-started/introduction.md)
2. [Terminology](getting-started/terminology.md)
3. [Laravel 4 Installation](getting-started/laravel-4.md)
4. [Laravel 5 Installation](getting-started/laravel-5.md)
5. [Lumen Installation](getting-started/lumen.md)
6. [Configuration](getting-started/config.md)
7. [Middlewares](getting-started/middlewares.md)
8. [Apache ModRewrite](getting-started/apache.md)

#### Authorization Server

1. [Choosing a Grant](authorization-server/choosing-grant.md)
2. Implementing an Authorization Server
    1. [With the Client Credentials Grant](authorization-server/client-credentials.md)
    2. [With the Password Grant](authorization-server/password.md)
    3. [With the Auth Code Grant](authorization-server/auth-code.md)
    4. [With the Refresh Token Grant](authorization-server/refresh-token.md)
3. Extending the server
    1. Using a different storage
    2. [Creating your own grant type](authorization-server/custom.md)

#### Resource Server

1. [Securing your API endpoints](resource-server/securing-endpoints.md)
    2. [Defining scopes](resource-server/securing-endpoints.md#defining-scopes)
    3. [Checking the access token](resource-server/securing-endpoints.md#checking-the-access-token)
    4. [Checking the scopes](resource-server/securing-endpoints.md#checking-the-scopes)

## Articles & Resources

- [The OAuth 2.0 authorization framework specification](https://tools.ietf.org/html/rfc6749)
- [The PHP League's official documentation](http://oauth2.thephpleague.com)
- [OAuth 2 Simplified by Aaron Parecki](https://aaronparecki.com/articles/2012/07/29/1/oauth2-simplified)

## Contributing

We welcome any pull request to improve the documentation. Please see our [contributing guidelines](../CONTRIBUTING.md).

## License

The Laravel OAuth 2.0 package is released under [the MIT License](../LICENSE).
