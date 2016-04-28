# Lumen

Composer is the recommended way to install this package. Add the following line to your composer.json file:

```php
"lucadegasperi/oauth2-server-laravel": "^5.0"
```
Then run composer update to get the package.

### Register package

In your `bootstrap/app.php` register service providers

```php
$app->register(\LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class);
$app->register(\LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider::class);
```

... and middleware

```php
$app->middleware([
    \LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class
]);
```

... and route middleware

```php
$app->routeMiddleware([
    'check-authorization-params' => \LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware::class,
    'oauth' => \LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
    'oauth-client' => \LucaDegasperi\OAuth2Server\Middleware\OAuthClientOwnerMiddleware::class,
    'oauth-user' => \LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware::class,
]);
```

... and Authorizer alias
```php
class_alias(\LucaDegasperi\OAuth2Server\Facades\Authorizer::class, 'Authorizer');
```

### Copy config

Copy `vendor/lucadegasperi/oauth2-server-laravel/config/oauth2.php` to your own config folder (`config/oauth2.php` in your project root). It has to be the correct config folder as it is registered using `$app->configure()`.

### Migrate

First copy the migrations from `vendor/lucadegasperi/oauth2-server-laravel/database/migrations` to your applications `database/migrations` directory.

Uncomment `$app->withEloquent();` and run `php artisan migrate`.

If you get an error saying the Config class can not be found, add `class_alias('Illuminate\Support\Facades\Config', 'Config');` to your `bootstrap/app.php` file and uncomment `$app->withFacades();` temporarily to import the migrations.

---

[&larr; Back to start](../README.md)
