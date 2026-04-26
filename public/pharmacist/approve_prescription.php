<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('pharmacist');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect_to('pharmacist/pharmacist_dashboard.php'); }
try {
    verify_csrf_or_die();
    $id = post_int('id');
    $action = post_enum('action', ['approve', 'reject']);
    $status = $action === 'approve' ? 'approved' : 'rejected';
    $pdo = getPDO();
    $stmt = $pdo->prepare('UPDATE Prescriptions SET status = ? WHERE prescription_id = ?');
    $stmt->execute([$status, $id]);
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}
redirect_to('pharmacist/pharmacist_dashboard.php');
