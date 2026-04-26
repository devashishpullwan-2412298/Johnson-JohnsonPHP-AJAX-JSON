<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('products.php');
}

try {
    verify_csrf_or_die();

    $medicine_id = post_int('medicine_id');
    $quantity = post_int('quantity');
    $user_id = (int) $_SESSION['user_id'];

    $db = getPDO();
    $stock_check = $db->prepare('SELECT stock FROM Medicines WHERE medicine_id = ? AND is_deleted = 0');
    $stock_check->execute([$medicine_id]);
    $stock = $stock_check->fetchColumn();

    if ($stock === false) {
        throw new RuntimeException('Medicine not found.');
    }

    $cart_check = $db->prepare('SELECT quantity FROM cart WHERE user_id = ? AND medicine_id = ?');
    $cart_check->execute([$user_id, $medicine_id]);
    $current_quantity = $cart_check->fetchColumn();

    if ($current_quantity === false) {
        $new_quantity = min((int) $stock, $quantity);
        $insert = $db->prepare('INSERT INTO cart (user_id, medicine_id, quantity, created_at) VALUES (?, ?, ?, NOW())');
        $insert->execute([$user_id, $medicine_id, $new_quantity]);
    } else {
        $new_quantity = min((int) $stock, (int) $current_quantity + $quantity);
        $update = $db->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND medicine_id = ?');
        $update->execute([$new_quantity, $user_id, $medicine_id]);
    }

    $_SESSION['flash_success'] = 'Item added to cart.';
    redirect_to('cart.php');
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    redirect_to('products.php');
}
