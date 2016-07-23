# Setting-up USBWebserver

## settings/php.ini

Uncomment and set timezone. E.g. `date.timezone = Europe/Kiev`

## settings/httpd.conf

Add as a last section:
```
<IfModule rewrite_module>
    RewriteEngine on
    RewriteCond %{SCRIPT_FILENAME} !^/(css/|font-awesome/|fonts/|images/|js/|phpmyadmin/)
    RewriteRule ^(.+)$ /index.php/$1 [L]
</IfModule>
```