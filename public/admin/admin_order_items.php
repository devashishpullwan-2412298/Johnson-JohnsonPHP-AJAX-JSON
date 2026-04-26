<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
try { $order_id = get_int('order_id'); } catch (Throwable $e) { exit('Order ID missing.'); }
$db = getPDO();
$stmt = $db->prepare('SELECT m.name AS medicine_name, oi.quantity, m.price, oi.subtotal FROM OrderItems oi JOIN Medicines m ON oi.medicine_id = m.medicine_id WHERE oi.order_id = ?');
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html><html><head><title>Order #<?= esc($order_id) ?> Items</title><link rel="stylesheet" href="../../assets/css/styles.css"></head><body>
<?php include __DIR__ . '/../navbar.php'; ?>
<h2>Order #<?= esc($order_id) ?> Items</h2>
<table border="1" cellpadding="10"><tr><th>Medicine</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
<?php if (!$items): ?><tr><td colspan="4">No items found for this order.</td></tr><?php else: foreach ($items as $item): ?>
<tr><td><?= esc($item['medicine_name']) ?></td><td><?= (int)$item['quantity'] ?></td><td>Rs <?= number_format((float)$item['price'], 2) ?></td><td>Rs <?= number_format((float)$item['subtotal'], 2) ?></td></tr>
<?php endforeach; endif; ?>
</table><br><a href="admin_orders.php">← Back to Orders</a>
</body></html>
