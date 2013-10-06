# PHP OAuth 2.0 Server for Laravel

[![Latest Unstable Version](https://poser.pugx.org/lucadegasperi/oauth2-server-laravel/v/unstable.png)](https://packagist.org/packages/lucadegasperi/oauth2-server-laravel) [![Build Status](https://travis-ci.org/lucadegasperi/oauth2-server-laravel.png?branch=master)](https://travis-ci.org/lucadegasperi/oauth2-server-laravel) [![Coverage Status](https://coveralls.io/repos/lucadegasperi/oauth2-server-laravel/badge.png)](https://coveralls.io/r/lucadegasperi/oauth2-server-laravel)

A wrapper package for the standards compliant OAuth 2.0 authorization server and resource server written in PHP by the [League of Extraordinary Packages](http://www.thephpleague.com).

The package assumes you have a good-enough knowledge of the principles behind the [OAuth 2.0 Specification](http://tools.ietf.org/html/rfc6749).

## Package Installation

### With Laravel Package Installer

The easiest way to install this package is via [Laravel Package Installer](https://github.com/rtablada/package-installer), this will set all the service providers and aliases for you. Run this artisan command to install the package:

```
php artisan package:install lucadegasperi/oauth2-server-laravel
```

### Manual Install

alternatively, you can manually install the package via composer. add the following line to your composer.json file:

```javascript
"lucadegasperi/oauth2-server-laravel": "dev-master"
```

Add this line of code to the ```providers``` array located in your ```app/config/app.php``` file:
```php
'LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider',
```

And this lines to the ```facades``` array:
```php
'AuthorizationServer' => 'LucaDegasperi\OAuth2Server\Facades\AuthorizationServerFacade',
'ResourceServer' => 'LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade',
```

### Configuration

In order to use the OAuth2 server publish its configuration first

```
php artisan config:publish lucadegasperi/oauth2-server-laravel
```

Afterwards edit the file ```app/config/packages/lucadegasperi/oauth2-server-laravel/oauth2.php``` to suit your needs.

### Migrations

This package comes with all the migrations you need to run a full featured oauth2 server. Run:

```
php artisan migrate --package="lucadegasperi/oauth2-server-laravel"
```

## Issuing access tokens

TBD

## Securing the API endpoints

You can protect your laravel routes with oauth by applying the ```oauth``` before filter to them like in the example shown below

```php
Route::get('secure-route', array('before' => 'oauth', function(){
    return "oauth secured route";
}));
```

Additionaly you can provide the allowed scopes to the ```oauth``` before filter by passing them in the filter name.

```php
Route::get('secure-route', array('before' => 'oauth|scope1,scope2', function(){
    return "oauth secured route";
}));
```

An interesting addition is the possibility to limit an endpoint to a specific owner type when using the client credentials grant type. It can be achieved by adding the ```oauth-owner``` before filter to your route.

```php
Route::get('secure-route', array('before' => array('oauth|scope1,scope2', 'oauth-owner|client'), function(){
    return "oauth secured route for clients only";
}));
```


The aim of this package is to make working with oauth2 server stuff in Laravel a breeze. You can still access all the undelying power of the league/oauth2-server package via the ```ResourceServer``` facade.

## Support

Bugs and feature request are tracked on [GitHub](https://github.com/lucadegasperi/oauth2-server-laravel/issues)

## License

This package is released under the MIT License.

## Credits

The code on which this package is [based](https://github.com/php-loep/oauth2-server/), is principally developed and maintained by [Alex Bilbie](https://twitter.com/alexbilbie).
