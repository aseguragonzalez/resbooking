<?php

$path = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])
    ? $_SERVER['REQUEST_URI']
    : '';
if (preg_match('/\.(css|jpe?g|png|gif|webp|svg|ico|js)$/i', $path)) {
    $fullPath = __DIR__ . $path;
    if (file_exists($fullPath) && is_file($fullPath)) {
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css'  => 'text/css',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'js'   => 'application/javascript',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
            'ico'  => 'image/x-icon',
        ];
        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $contentType);
        readfile($fullPath);
        exit;
    }
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
