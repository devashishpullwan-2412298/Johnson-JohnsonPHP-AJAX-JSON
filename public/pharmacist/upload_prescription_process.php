<?php
require_once __DIR__ . '/../../src/config.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect_to('my_orders.php'); }
$storedPath = null;
try {
    verify_csrf_or_die();
    $order_id = post_int('order_id');
    $user_id  = (int)$_SESSION['user_id'];
    $pdo = getPDO();
    get_prescription_upload_context($pdo, $order_id, $user_id);

    if (!isset($_FILES['prescription'])) {
        throw new RuntimeException('Please choose a prescription file to upload.');
    }

    $storedPath = store_prescription_upload($_FILES['prescription']);

    $pdo->beginTransaction();
    $oldPath = save_prescription_record($pdo, $order_id, $user_id, $storedPath);
    $pdo->commit();

    if ($oldPath !== null && $oldPath !== '' && $oldPath !== $storedPath) {
        delete_prescription_upload($oldPath);
    }

    $_SESSION['flash_success'] = 'Prescription uploaded successfully.';
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if ($storedPath !== null) {
        delete_prescription_upload($storedPath);
    }
    if ($e instanceof RuntimeException) {
        $_SESSION['flash_error'] = $e->getMessage();
    } else {
        error_log('Prescription upload failed: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'The prescription upload could not be completed. Please try again.';
    }
}
redirect_to('my_orders.php');
