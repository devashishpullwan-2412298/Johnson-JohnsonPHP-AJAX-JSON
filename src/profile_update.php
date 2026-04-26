<?php
require_once __DIR__ . '/config.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('profile.php');
}
try {
    verify_csrf_or_die();
    $id = post_int('id');
    if ($id !== (int)$_SESSION['user_id']) {
        throw new RuntimeException('Unauthorized profile update.');
    }
    $name = post_string('full_name', 100);
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = post_string('phone', 20, false);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Invalid email address.');
    }
    $stmt = getPDO()->prepare('UPDATE Users SET name = ?, email = ?, phone = ? WHERE user_id = ?');
    $stmt->execute([$name, $email, $phone ?: null, $id]);
    $_SESSION['name'] = $name;
    $_SESSION['flash_success'] = 'Profile updated successfully.';
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('profile.php');
