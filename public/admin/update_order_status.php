<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect_to('admin/admin_orders.php'); }
try {
    verify_csrf_or_die();
    $order_id = post_int('order_id');
    $status = post_enum('status', ['Pending', 'Approved', 'Shipped', 'Completed', 'Cancelled']);
    $db = getPDO();
    $stmt = $db->prepare('UPDATE Orders SET status = ? WHERE order_id = ?');
    $stmt->execute([$status, $order_id]);
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('admin/admin_orders.php');
