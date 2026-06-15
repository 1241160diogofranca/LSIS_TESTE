<?php
// PHP built-in server router. Serves static assets directly; otherwise forwards
// to index.php which handles routing.

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$docroot = __DIR__;

// Serve assets directly
if ($uri !== '/' && file_exists($docroot . $uri)) {
    $ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
    $mime = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico'  => 'image/x-icon',
        'pdf'  => 'application/pdf',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
    ];
    if (isset($mime[$ext])) {
        header('Content-Type: ' . $mime[$ext]);
        readfile($docroot . $uri);
        return true;
    }
    if ($ext === 'php') {
        // disallow direct execution of internal php files (only index.php is entry)
        http_response_code(403);
        return true;
    }
}

// Everything else -> front controller
require __DIR__ . '/index.php';
return true;
