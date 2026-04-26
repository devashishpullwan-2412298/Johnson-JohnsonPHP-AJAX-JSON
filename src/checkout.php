<?php
require_once __DIR__ . '/config.php';
requireLogin();

$pdo = getPDO();

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT c.medicine_id, c.quantity, m.price
    FROM cart c
    JOIN Medicines m ON m.medicine_id = c.medicine_id
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($items)) {
    die("Cart is empty.");
}

// Calculate total
$total = 0;
foreach ($items as $i) {
    $total += $i['price'] * $i['quantity'];
}

try {
    $pdo->beginTransaction();

    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, created_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$_SESSION['user_id'], $total]);

    $order_id = $pdo->lastInsertId();

    // Insert order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, medicine_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($items as $i) {
        $stmt->execute([
            $order_id,
            $i['medicine_id'],
            $i['quantity'],
            $i['price']
        ]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    $pdo->commit();

    header("Location: ../public/my_orders.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Checkout Error: " . $e->getMessage());
}
?>