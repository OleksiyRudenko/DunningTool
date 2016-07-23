# Installation under USBWebserver

 1. Install [USB Webserver](http://www.usbwebserver.net/en/)
 1. Set-up USB Webserver (see below)
 1. Check if USB Webserver runs properly
 1. Quit USB Webserver
 1. Find yourself in USB Webserver installation directory and:
    - Delete `root` directory
    - Run `git clone ` _`this-repo-url`_ `root` to have this project installed under your web-server root
 1. Launch USB Webserver and tap `Localhost` button

## Setting-up USBWebserver

### settings/php.ini

Uncomment and set timezone. E.g. `date.timezone = Europe/Kiev`

### settings/httpd.conf

Add as a last section:
```
<IfModule rewrite_module>
    RewriteEngine on
    RewriteCond %{SCRIPT_FILENAME} !^/(css/|font-awesome/|fonts/|images/|js/|phpmyadmin/)
    RewriteRule ^(.+)$ /index.php/$1 [L]
</IfModule>
```