# PHP OAuth 2.0 Server for Laravel

A wrapper package for the standards compliant OAuth 2.0 authorization server and resource server written in PHP by the [League of Extraordinary Packages](http://www.thephpleague.com)

## Package Installation

The library is provided as a Composer package which can be installed by adding the package to your composer.json file:

```javascript
{
	"require": {
		"lucadegasperi/oauth2-server-laravel": "dev-master"
	}
}
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

## Support

Bugs and feature request are tracked on [GitHub](https://github.com/lucadegasperi/oauth2-server-laravel/issues)

## License

This package is released under the MIT License.

## Credits

The code on which this package is [based](https://github.com/php-loep/oauth2-server/), is principally developed and maintained by [Alex Bilbie](https://twitter.com/alexbilbie).
