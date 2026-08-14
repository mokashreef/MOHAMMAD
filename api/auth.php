<?php
require_once __DIR__ . '/config.php';

$method = getMethod();

if ($method === 'POST') {
    // Login
    $data = getJsonBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        sendJson(['message' => 'يرجى إدخال البريد الإلكتروني وكلمة المرور'], 400);
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        sendJson(['message' => 'بيانات الدخول غير صحيحة'], 401);
    }

    $token = jwt_encode(['id' => $user['id'], 'email' => $user['email']]);

    sendJson([
        '_id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'token' => $token
    ]);

} elseif ($method === 'GET') {
    // Get current user
    $payload = authenticate();
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([$payload['id']]);
    $user = $stmt->fetch();

    if (!$user) {
        sendJson(['message' => 'المستخدم غير موجود'], 404);
    }

    sendJson($user);
} else {
    sendJson(['message' => 'Method not allowed'], 405);
}
