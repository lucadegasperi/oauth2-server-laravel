# Apache ModRewrite

If you are using Apache, you might notice that your Authorization headers are not making it through with the request.  Many hosts do not allow this header through by default, and Apache is no exception.

Open up your .htaccess file in /public and add the following lines of code before the Front Controller block:

```sh
# Authorization Headers
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

Your full `.htaccess` file should look like this after the change:

```sh
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    RewriteEngine On

    # Redirect Trailing Slashes...
    RewriteRule ^(.*)/$ /$1 [L,R=301]
    # Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

[&larr; Back to start](../README.md)
