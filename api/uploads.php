<?php
require_once __DIR__ . '/config.php';

// GET - list all uploaded images
authenticate();

$uploadsDir = __DIR__ . '/../uploads/';
$images = [];

if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExt)) {
            $images[] = [
                'filename' => $file,
                'path' => '/uploads/' . $file,
                'size' => filesize($uploadsDir . $file)
            ];
        }
    }
}

sendJson($images);
