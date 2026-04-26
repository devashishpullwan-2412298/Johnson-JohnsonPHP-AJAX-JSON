<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('checkout.php');
}

$pdo = getPDO();
$user_id = (int) $_SESSION['user_id'];
$storedPrescriptionPath = null;

try {
    verify_csrf_or_die();

    $address = post_string('address', 500);
    $phone = post_string('phone', 20);
    $order_type = post_enum('order_type', ['Pickup', 'Delivery', 'Online']);
    $payment_method = post_enum('payment_method', ['cod', 'mock_card']);

    $stmt = $pdo->prepare('SELECT c.medicine_id, c.quantity, m.price, m.prescription_needed FROM cart c JOIN Medicines m ON c.medicine_id = m.medicine_id WHERE c.user_id = ?');
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();

    if (!$items) {
        throw new RuntimeException('Cart is empty.');
    }

    $total_price = 0;
    $requires_prescription = false;

    foreach ($items as $item) {
        $total_price += (float) $item['price'] * (int) $item['quantity'];
        if ((int) $item['prescription_needed'] === 1) {
            $requires_prescription = true;
        }
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO Orders (user_id, total_price, order_date, status, order_type, address, phone) VALUES (?, ?, NOW(), 'Pending', ?, ?, ?)");
    $stmt->execute([$user_id, $total_price, $order_type, $address, $phone]);
    $order_id = (int) $pdo->lastInsertId();

    $item_stmt = $pdo->prepare('INSERT INTO OrderItems (order_id, medicine_id, quantity, subtotal) VALUES (?, ?, ?, ?)');
    foreach ($items as $item) {
        $subtotal = (float) $item['price'] * (int) $item['quantity'];
        $item_stmt->execute([$order_id, (int) $item['medicine_id'], (int) $item['quantity'], $subtotal]);
    }

    if ($requires_prescription) {
        if (!isset($_FILES['prescription_file'])) {
            throw new RuntimeException('Prescription file is required.');
        }

        $storedPrescriptionPath = store_prescription_upload($_FILES['prescription_file']);
        $stmt = $pdo->prepare("INSERT INTO Prescriptions (order_id, user_id, file_path, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$order_id, $user_id, $storedPrescriptionPath]);
        $pdo->prepare("UPDATE Orders SET status = 'Pending' WHERE order_id = ?")->execute([$order_id]);
    }

    $payment_status = 'Pending';
    $paid_at = null;
    if ($payment_method === 'mock_card') {
        $payment_status = 'Paid';
        $paid_at = date('Y-m-d H:i:s');
    }

    $stmt = $pdo->prepare('INSERT INTO Payments (order_id, amount, method, status, paid_at, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$order_id, $total_price, $payment_method, $payment_status, $paid_at]);

    $pdo->prepare('DELETE FROM cart WHERE user_id = ?')->execute([$user_id]);
    $pdo->commit();

    if ($requires_prescription) {
        $_SESSION['flash_success'] = 'Order placed and prescription uploaded successfully.';
    } elseif ($payment_method === 'mock_card') {
        $_SESSION['flash_success'] = 'Payment successful. Your order has been placed.';
    } else {
        $_SESSION['flash_success'] = 'Order placed successfully.';
    }

    redirect_to('my_orders.php');
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ($storedPrescriptionPath !== null) {
        delete_prescription_upload($storedPrescriptionPath);
    }

    $_SESSION['flash_error'] = $e->getMessage();
    redirect_to('checkout.php');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ($storedPrescriptionPath !== null) {
        delete_prescription_upload($storedPrescriptionPath);
    }

    error_log('Checkout failed: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Checkout could not be completed right now. Please try again.';
    redirect_to('checkout.php');
}
