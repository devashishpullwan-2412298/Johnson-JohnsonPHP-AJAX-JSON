<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('cart.php');
}

try {
    verify_csrf_or_die();

    $cart_id = post_int('id');
    $user_id = (int) $_SESSION['user_id'];

    $stmt = getPDO()->prepare('DELETE FROM cart WHERE cart_id = ? AND user_id = ?');
    $stmt->execute([$cart_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['flash_success'] = 'Item removed from cart.';
    }
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

redirect_to('cart.php');
