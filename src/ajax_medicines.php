<?php
require_once __DIR__ . '/config.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');

$pdo = getPDO();
$search = trim((string) ($_GET['search'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$prescriptionOnly = (string) ($_GET['prescription_only'] ?? '') === '1';

$sql = 'SELECT medicine_id, name, description, category, price, stock, prescription_needed
        FROM Medicines
        WHERE is_deleted = 0';
$params = [];

if ($search !== '') {
    $sql .= ' AND (name LIKE :search OR description LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

if ($category !== '') {
    $sql .= ' AND category = :category';
    $params[':category'] = $category;
}

if ($prescriptionOnly) {
    $sql .= ' AND prescription_needed = 1';
}

$sql .= ' ORDER BY name ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medicines = array_map(
    static function (array $medicine): array {
        return [
            'medicine_id' => (int) $medicine['medicine_id'],
            'name' => (string) $medicine['name'],
            'description' => $medicine['description'] !== null ? (string) $medicine['description'] : '',
            'category' => $medicine['category'] !== null ? (string) $medicine['category'] : 'General',
            'price' => (float) $medicine['price'],
            'stock' => (int) $medicine['stock'],
            'prescription_needed' => (bool) $medicine['prescription_needed'],
        ];
    },
    $stmt->fetchAll()
);

echo json_encode([
    'filters' => [
        'search' => $search,
        'category' => $category,
        'prescription_only' => $prescriptionOnly,
    ],
    'count' => count($medicines),
    'medicines' => $medicines,
], JSON_UNESCAPED_SLASHES);
