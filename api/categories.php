<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$db = getDB();
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
    $stmt = $db->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
    sendJson(array_map(function($c) {
        return [
            '_id' => $c['id'],
            'name' => $c['name'],
            'nameEn' => $c['nameEn'],
            'slug' => $c['slug']
        ];
    }, $categories));

} elseif ($method === 'POST') {
    authenticate();
    $data = getJsonBody();

    $stmt = $db->prepare("INSERT INTO categories (name, nameEn, slug) VALUES (?, ?, ?)");
    $stmt->execute([
        $data['name'] ?? '',
        $data['nameEn'] ?? '',
        $data['slug'] ?? ''
    ]);

    sendJson([
        '_id' => $db->lastInsertId(),
        'name' => $data['name'],
        'nameEn' => $data['nameEn'],
        'slug' => $data['slug']
    ], 201);

} elseif ($method === 'PUT') {
    authenticate();
    if (!$id) sendJson(['message' => 'ID مطلوب'], 400);
    $data = getJsonBody();

    $stmt = $db->prepare("UPDATE categories SET name=?, nameEn=?, slug=? WHERE id=?");
    $stmt->execute([
        $data['name'] ?? '',
        $data['nameEn'] ?? '',
        $data['slug'] ?? '',
        $id
    ]);

    sendJson([
        '_id' => $id,
        'name' => $data['name'],
        'nameEn' => $data['nameEn'],
        'slug' => $data['slug']
    ]);

} elseif ($method === 'DELETE') {
    authenticate();
    if (!$id) sendJson(['message' => 'ID مطلوب'], 400);

    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    sendJson(['message' => 'تم حذف التصنيف بنجاح']);

} else {
    sendJson(['message' => 'Method not allowed'], 405);
}
