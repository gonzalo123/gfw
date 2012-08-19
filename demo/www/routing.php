<?php
/*
 * This file is only needed to use the built-in PHP5.4 webserver
 * We can run in with:
 *
 * php -S localhost:8000 routing.php
 *
 * If you run apache webserver you need mod-rewrite with the following .htaccess:
 *
 * <IfModule mod_rewrite.c>
 *    Options -MultiViews
 *    RewriteEngine On
 *    RewriteCond %{REQUEST_FILENAME} !-f
 *    RewriteRule ^(.*)$ index.php [QSA,L]
 </IfModule>
 *
 */

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false; // serve the requested resource as-is.
} else {
    include __DIR__ . '/index.php';
}