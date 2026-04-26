<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();
try {
    $order_id = get_int('id');
} catch (Throwable $e) {
    exit('Order not found.');
}
$pdo = getPDO();
$user_id = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT oi.quantity, oi.subtotal, m.name, m.price FROM OrderItems oi INNER JOIN Orders o ON oi.order_id = o.order_id INNER JOIN Medicines m ON oi.medicine_id = m.medicine_id WHERE oi.order_id = ? AND o.user_id = ?');
$stmt->execute([$order_id, $user_id]);
$items = $stmt->fetchAll();
$total = 0.0;
foreach ($items as $item) {
    $total += (float)$item['subtotal'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="page-container customer-shell">
    <section class="customer-hero">
        <div class="hero-copy">
            <p class="eyebrow">Order Breakdown</p>
            <h1>Order #<?= (int)$order_id ?></h1>
            <p class="muted">Review the item-by-item details for this order, including the quantity selected and the subtotal charged for each medicine.</p>
        </div>
        <div class="hero-stats">
            <div class="stat-chip">
                <strong><?= count($items) ?></strong>
                <span>Line items</span>
            </div>
            <div class="stat-chip">
                <strong>Rs <?= number_format($total, 2) ?></strong>
                <span>Order total</span>
            </div>
        </div>
    </section>

    <section class="table-card">
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <h2>No order details found</h2>
                <p class="muted">This order could not be loaded, or it does not belong to the current account.</p>
            </div>
        <?php else: ?>
            <table class="detail-table">
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td><?= esc($row['name']) ?></td>
                        <td><?= (int)$row['quantity'] ?></td>
                        <td>Rs <?= number_format((float)$row['price'], 2) ?></td>
                        <td>Rs <?= number_format((float)$row['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
