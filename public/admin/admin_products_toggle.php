<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect_to('admin/admin_products.php'); }
try {
    verify_csrf_or_die();
    $id = post_int('id');
    $action = post_enum('action', ['delete', 'restore']);
    $is_deleted = ($action === 'delete') ? 1 : 0;
    $stmt = getPDO()->prepare('UPDATE Medicines SET is_deleted = ? WHERE medicine_id = ?');
    $stmt->execute([$is_deleted, $id]);
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('admin/admin_products.php');
