# Laravel 4

Composer is the recommended way to install this package. Add the following line to your `composer.json` file:

```json
"lucadegasperi/oauth2-server-laravel": "^3.0"
```

Then run `composer update` to get the package.

> **Note:** If installation fails set `"minimum-stability": "dev"` in your `composer.json` file.

Once composer has installed the package add this line of code to the `providers` array located in your `app/config/app.php` file:
```php
'LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider',
'LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider',
```

And this lines to the `aliases` array:
```php
'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade',
```

## Configuration Publishing

In order to customize the behavior of this package, a configuration file to publish is provided to you.

```bash
php artisan config:publish lucadegasperi/oauth2-server-laravel
```

Afterwards you can edit the file `app/config/packages/lucadegasperi/oauth2-server-laravel/oauth2.php` to suit your needs. A description of the configuration fields is [described here](https://github.com/lucadegasperi/oauth2-server-laravel/wiki/Configuration-Options).

## Migrations

This package comes with all the database tables you need to run a full featured OAuth 2.0 server. Run the migrations command to get them into your application installation

```bash
php artisan oauth2-server:migrations
```

## Sample Controller

To make your life easier, this package comes with a sample controller you can use to get started with your OAuth 2.0 Server. The `controller` command will publish the controller into your `app/controllers` directory.

```bash
php artisan oauth2-server:controller
```

---

[&larr; Back to start](../README.md)
