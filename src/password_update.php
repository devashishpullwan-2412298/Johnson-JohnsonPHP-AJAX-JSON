<?php
require_once __DIR__ . '/config.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('profile.php');
}
try {
    verify_csrf_or_die();
    $newpass = (string)($_POST['new_password'] ?? '');
    if (strlen($newpass) < 8) {
        throw new RuntimeException('Password must be at least 8 characters.');
    }
    $hash = password_hash($newpass, PASSWORD_DEFAULT);
    $stmt = getPDO()->prepare('UPDATE Users SET password = ? WHERE user_id = ?');
    $stmt->execute([$hash, (int)$_SESSION['user_id']]);
    $_SESSION['flash_success'] = 'Password changed successfully.';
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('profile.php');
