<?php
require_once __DIR__ . '/config.php';

$method = getMethod();

if ($method === 'POST') {
    authenticate();

    if (!isset($_FILES['image'])) {
        sendJson(['message' => 'يرجى رفع صورة'], 400);
    }

    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    if (!in_array($file['type'], $allowedTypes)) {
        sendJson(['message' => 'نوع الملف غير مدعوم'], 400);
    }

    if ($file['size'] > $maxSize) {
        sendJson(['message' => 'حجم الملف أكبر من 10MB'], 400);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '-' . mt_rand(100000, 999999) . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        sendJson([
            'message' => 'تم رفع الصورة بنجاح',
            'filename' => $filename,
            'path' => '/uploads/' . $filename
        ]);
    } else {
        sendJson(['message' => 'فشل رفع الصورة'], 500);
    }

} elseif ($method === 'DELETE') {
    authenticate();

    $filename = $_GET['filename'] ?? '';
    if (!$filename) {
        sendJson(['message' => 'اسم الملف مطلوب'], 400);
    }

    // Prevent directory traversal
    $filename = basename($filename);
    $filePath = __DIR__ . '/../uploads/' . $filename;

    if (!file_exists($filePath)) {
        sendJson(['message' => 'الصورة غير موجودة'], 404);
    }

    unlink($filePath);
    sendJson(['message' => 'تم حذف الصورة بنجاح']);

} else {
    sendJson(['message' => 'Method not allowed'], 405);
}
