# Creating a Custom Grant

To create your custom grant type follow Alex's guide.

> http://oauth2.thephpleague.com/authorization-server/custom-grants/

Registering your custom Grant with this package is easy:

Add the following properties to your `config/oauth.php`
```php
'grant_types' => [
    'custom_grant_identifier' => [
        'class' => 'Your\Custom\Grant\Namespace\And\Class'
    ]
]
```

---

[&larr; Back to start](../README.md)
