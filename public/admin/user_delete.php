<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect_to('admin/users.php'); }
try {
    verify_csrf_or_die();
    $id = post_int('user_id');
    getPDO()->prepare('DELETE FROM Users WHERE user_id = ? AND role <> "admin"')->execute([$id]);
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('admin/users.php');
