# PHP OAuth 2.0 Server for Laravel

[![Latest Stable Version](https://poser.pugx.org/lucadegasperi/oauth2-server-laravel/v/stable.png)](https://packagist.org/packages/lucadegasperi/oauth2-server-laravel) [![Build Status](https://scrutinizer-ci.com/g/lucadegasperi/oauth2-server-laravel/badges/build.png?b=rewrite)](https://scrutinizer-ci.com/g/lucadegasperi/oauth2-server-laravel/build-status/rewrite) [![Code Coverage](https://scrutinizer-ci.com/g/lucadegasperi/oauth2-server-laravel/badges/coverage.png?b=rewrite)](https://scrutinizer-ci.com/g/lucadegasperi/oauth2-server-laravel/?branch=rewrite) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lucadegasperi/oauth2-server-laravel/badges/quality-score.png?b=rewrite)](https://scrutinizer-ci.com/g/lucadegasperi/oauth2-server-laravel/?branch=rewrite)

[OAuth 2.0](http://tools.ietf.org/wg/oauth/draft-ietf-oauth-v2/) authorization server and resource server for the Laravel framework. Standard compliant thanks to the amazing work by [The League of Extraordinary Packages](http://www.thephpleague.com) OAuth 2.0 authorization server and resource server.

The package assumes you have a good-enough knowledge of the principles behind the [OAuth 2.0 Specification](http://tools.ietf.org/html/rfc6749).

## Version Compability

 Laravel  | OAuth Server | PHP 
:---------|:-------------|:----
 4.0.x    | 1.0.x        |>= 5.3
 4.1.x    | 1.0.x        |>= 5.3
 4.2.x    | 3.0.x        |>= 5.4
 5.0.x    | 4.0.x@dev    |>= 5.4

## Package Installation

You can install the package via composer. add the following line to your composer.json file:

```javascript
"lucadegasperi/oauth2-server-laravel": "3.0.x"
```

Add this line of code to the ```providers``` array located in your ```app/config/app.php``` file:
```php
'LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider',
'LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider',
```

And this lines to the ```aliases``` array:
```php
'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade',
```

## Documentation

This package features an [extensive wiki](https://github.com/lucadegasperi/oauth2-server-laravel/wiki) to help you getting started implementing an OAuth 2.0 Server in your Laravel app.

## Support

Bugs and feature request are tracked on [GitHub](https://github.com/lucadegasperi/oauth2-server-laravel/issues)

## License

This package is released under the MIT License.

## Credits

The code on which this package is [based](https://github.com/php-loep/oauth2-server/), is principally developed and maintained by [Alex Bilbie](https://twitter.com/alexbilbie).
