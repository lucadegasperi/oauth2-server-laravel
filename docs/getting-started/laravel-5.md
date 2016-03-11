# Laravel 5

Composer is the recommended way to install this package. Add the following line to your `composer.json` file:

```json
"lucadegasperi/oauth2-server-laravel": "5.1.*"
```

Then run `composer update` to get the package.

Once composer has installed the package add this line of code to the `providers` array located in your `config/app.php` file:
```php
LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class,
LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider::class,
```

Add this line to the `aliases` array:
```php
'Authorizer' => LucaDegasperi\OAuth2Server\Facades\Authorizer::class,
```

Add the following line to your `app/Http/Kernel.php` file in the `$middleware` array
```php
\LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class,
```
This will catch any OAuth error and respond appropriately.

Then add
```php
'oauth' => \LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
'oauth-user' => \LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware::class,
'oauth-client' => \LucaDegasperi\OAuth2Server\Middleware\OAuthClientOwnerMiddleware::class,
'check-authorization-params' => \LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware::class,
```
to the `$routeMiddleware` array.

In order to make some the authorization and resource server work correctly with Laravel5, remove the `App\Http\Middleware\VerifyCsrfToken` line from the `$middleware` array and place it in the `$routeMiddleware` array like this: `'csrf' => App\Http\Middleware\VerifyCsrfToken::class,`

> **Note:** remember to add the csrf middleware manually on any route where it's appropriate.

### Migrations and Configuration Publishing
Run `php artisan vendor:publish` to publish this package configuration and migrations. Afterwards you can edit the file `config/oauth2.php` to suit your needs.

> **Note:** migrations are only published, remember to run them when ready.

Run migration to create required tables

```bash
php artisan migrate
```

---

[&larr; Back to start](../README.md)
