<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect_to('admin/users.php'); }
try {
    verify_csrf_or_die();
    $id = (int)($_POST['user_id'] ?? 0);
    $name = post_string('name', 100);
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = post_string('phone', 20, false);
    $role = post_enum('role', ['customer', 'pharmacist', 'admin']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { throw new RuntimeException('Invalid email'); }
    if ($id > 0) {
        $stmt = getPDO()->prepare('UPDATE Users SET name = ?, email = ?, phone = ?, role = ? WHERE user_id = ?');
        $stmt->execute([$name, $email, $phone ?: null, $role, $id]);
    }
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('admin/users.php');
