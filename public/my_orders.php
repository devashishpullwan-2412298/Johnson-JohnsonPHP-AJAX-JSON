<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();

$stmt = getPDO()->prepare('SELECT order_id, order_date, total_price, status FROM Orders WHERE user_id = ? ORDER BY order_date DESC');
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
$error = flash_message('flash_error');
$success = flash_message('flash_success');
$pending_count = 0;

foreach ($orders as $order) {
    if (stripos($order['status'], 'pending') !== false) {
        $pending_count++;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="page-container customer-shell">
    <section class="customer-hero">
        <div class="hero-copy">
            <p class="eyebrow">Order Tracking</p>
            <h1>My Orders</h1>
            <p class="muted">Review recent purchases, open order details, and upload prescriptions for any order that is still waiting for verification.</p>
        </div>
        <div class="hero-stats">
            <div class="stat-chip">
                <strong><?= count($orders) ?></strong>
                <span>Total orders</span>
            </div>
            <div class="stat-chip">
                <strong><?= $pending_count ?></strong>
                <span>Pending uploads</span>
            </div>
        </div>
    </section>

    <?php if ($error): ?>
        <div class="alert-inline error"><?= esc($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert-inline success"><?= esc($success) ?></div>
    <?php endif; ?>

    <section class="table-card">
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <h2>No orders yet</h2>
                <p class="muted">Once you place an order, it will appear here with its current status and available actions.</p>
            </div>
        <?php else: ?>
            <table class="order-table">
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total (Rs)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $status = strtolower($order['status']);
                    $status_class = '';
                    if (strpos($status, 'pending') !== false) {
                        $status_class = 'pending';
                    } elseif (strpos($status, 'process') !== false) {
                        $status_class = 'processing';
                    } elseif (strpos($status, 'deliver') !== false || strpos($status, 'complete') !== false) {
                        $status_class = 'completed';
                    } elseif (strpos($status, 'cancel') !== false || strpos($status, 'reject') !== false) {
                        $status_class = 'cancelled';
                    }
                    ?>
                    <tr>
                        <td>#<?= (int) $order['order_id'] ?></td>
                        <td><?= esc($order['order_date']) ?></td>
                        <td><?= number_format((float) $order['total_price'], 2) ?></td>
                        <td><span class="status-pill <?= esc($status_class) ?>"><?= esc($order['status']) ?></span></td>
                        <td>
                            <div class="link-row">
                                <a class="action-link" href="order_details.php?id=<?= (int) $order['order_id'] ?>">View Details</a>
                                <?php if (stripos($order['status'], 'pending') !== false): ?>
                                    <a class="link-quiet" href="pharmacist/upload_prescription.php?order_id=<?= (int) $order['order_id'] ?>">Upload Prescription</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
<?php include __DIR__ . '/../src/templates/footer.php'; ?>