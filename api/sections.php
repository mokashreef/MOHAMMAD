<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$db = getDB();
$key = $_GET['key'] ?? null;

if ($method === 'GET') {
    if ($key) {
        $stmt = $db->prepare("SELECT * FROM sections WHERE section_key = ?");
        $stmt->execute([$key]);
        $section = $stmt->fetch();
        if (!$section) sendJson(['message' => 'القسم غير موجود'], 404);
        sendJson([
            '_id' => $section['id'],
            'key' => $section['section_key'],
            'content' => json_decode($section['content'], true)
        ]);
    } else {
        $stmt = $db->query("SELECT * FROM sections");
        $sections = $stmt->fetchAll();
        sendJson(array_map(function($s) {
            return [
                '_id' => $s['id'],
                'key' => $s['section_key'],
                'content' => json_decode($s['content'], true)
            ];
        }, $sections));
    }

} elseif ($method === 'PUT') {
    authenticate();
    if (!$key) sendJson(['message' => 'Key مطلوب'], 400);
    $data = getJsonBody();
    $content = json_encode($data['content'] ?? [], JSON_UNESCAPED_UNICODE);

    // Upsert
    $stmt = $db->prepare("SELECT id FROM sections WHERE section_key = ?");
    $stmt->execute([$key]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $db->prepare("UPDATE sections SET content = ? WHERE section_key = ?");
        $stmt->execute([$content, $key]);
    } else {
        $stmt = $db->prepare("INSERT INTO sections (section_key, content) VALUES (?, ?)");
        $stmt->execute([$key, $content]);
    }

    sendJson([
        'key' => $key,
        'content' => $data['content']
    ]);

} else {
    sendJson(['message' => 'Method not allowed'], 405);
}
