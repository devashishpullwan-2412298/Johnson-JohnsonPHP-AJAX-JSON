<?php
require_once '../../src/db.php';
require_once '../../includes/admin/auth_admin.php';

if (!isset($_POST['order_id'], $_POST['status'])) {
    die("Missing data");
}

$stmt = $db->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
$stmt->execute([$_POST['status'], $_POST['order_id']]);

header("Location: orders.php?msg=updated");
exit;