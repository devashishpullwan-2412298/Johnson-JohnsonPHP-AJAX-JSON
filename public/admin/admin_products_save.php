<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect_to('admin/admin_products.php'); }
try {
    verify_csrf_or_die();
    $pdo = getPDO();
    $id = (int)($_POST['medicine_id'] ?? 0);
    $name = post_string('name', 150);
    $description = post_string('description', 1000);
    $category = post_string('category', 80);
    $price = (float)($_POST['price'] ?? -1);
    $stock = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
    $prescription_needed = isset($_POST['prescription_needed']) ? 1 : 0;
    if ($price < 0 || $stock === false || $stock < 0) { throw new RuntimeException('Invalid price or stock.'); }
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE Medicines SET name = ?, description = ?, category = ?, price = ?, stock = ?, prescription_needed = ? WHERE medicine_id = ?');
        $stmt->execute([$name, $description, $category, $price, $stock, $prescription_needed, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO Medicines (name, description, category, price, stock, prescription_needed) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $description, $category, $price, $stock, $prescription_needed]);
    }
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('admin/admin_products.php');
