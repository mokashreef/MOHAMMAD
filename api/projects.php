<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$db = getDB();

// Parse ID from query string
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
    // Get all projects (public)
    if ($id) {
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.nameEn as category_nameEn, c.slug as category_slug, c.id as category_id
            FROM projects p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        if (!$project) sendJson(['message' => 'المشروع غير موجود'], 404);
        sendJson(formatProject($project));
    } else {
        $stmt = $db->query("
            SELECT p.*, c.name as category_name, c.nameEn as category_nameEn, c.slug as category_slug, c.id as category_id
            FROM projects p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.sort_order ASC, p.created_at DESC
        ");
        $projects = $stmt->fetchAll();
        sendJson(array_map('formatProject', $projects));
    }

} elseif ($method === 'POST') {
    authenticate();
    $data = getJsonBody();

    $stmt = $db->prepare("
        INSERT INTO projects (title, titleEn, description, descriptionEn, image, link, category_id, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['title'] ?? '',
        $data['titleEn'] ?? '',
        $data['description'] ?? '',
        $data['descriptionEn'] ?? '',
        $data['image'] ?? '',
        $data['link'] ?? '#',
        $data['category'] ?? null,
        $data['order'] ?? 0
    ]);

    $newId = $db->lastInsertId();
    // Fetch the created project with category
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name, c.nameEn as category_nameEn, c.slug as category_slug, c.id as category_id
        FROM projects p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$newId]);
    sendJson(formatProject($stmt->fetch()), 201);

} elseif ($method === 'PUT') {
    authenticate();
    if (!$id) sendJson(['message' => 'ID مطلوب'], 400);
    $data = getJsonBody();

    $stmt = $db->prepare("
        UPDATE projects SET title=?, titleEn=?, description=?, descriptionEn=?, image=?, link=?, category_id=?, sort_order=?
        WHERE id=?
    ");
    $stmt->execute([
        $data['title'] ?? '',
        $data['titleEn'] ?? '',
        $data['description'] ?? '',
        $data['descriptionEn'] ?? '',
        $data['image'] ?? '',
        $data['link'] ?? '#',
        $data['category'] ?? null,
        $data['order'] ?? 0,
        $id
    ]);

    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name, c.nameEn as category_nameEn, c.slug as category_slug, c.id as category_id
        FROM projects p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) sendJson(['message' => 'المشروع غير موجود'], 404);
    sendJson(formatProject($project));

} elseif ($method === 'DELETE') {
    authenticate();
    if (!$id) sendJson(['message' => 'ID مطلوب'], 400);

    $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    sendJson(['message' => 'تم حذف المشروع بنجاح']);

} else {
    sendJson(['message' => 'Method not allowed'], 405);
}

// Format project to match frontend expected structure
function formatProject($row) {
    return [
        '_id' => $row['id'],
        'title' => $row['title'],
        'titleEn' => $row['titleEn'],
        'description' => $row['description'],
        'descriptionEn' => $row['descriptionEn'],
        'image' => $row['image'],
        'link' => $row['link'],
        'order' => (int)$row['sort_order'],
        'createdAt' => $row['created_at'],
        'category' => $row['category_id'] ? [
            '_id' => $row['category_id'],
            'name' => $row['category_name'],
            'nameEn' => $row['category_nameEn'],
            'slug' => $row['category_slug']
        ] : null
    ];
}
