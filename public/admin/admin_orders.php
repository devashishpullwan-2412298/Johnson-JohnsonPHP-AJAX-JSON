<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
$db = getPDO();
$orders = $db->query('SELECT order_id, user_id, order_date, total_price, status FROM Orders ORDER BY order_date DESC')->fetchAll();
$statuses = ['Pending', 'Approved', 'Shipped', 'Completed', 'Cancelled'];
?>
<!DOCTYPE html><html><head><title>Admin Orders</title><link rel="stylesheet" href="../../assets/css/styles.css"></head><body>
<?php include __DIR__ . '/../navbar.php'; ?>
<h2 class="admin-section-title">Manage Orders</h2>
<table class="admin-table"><tr><th>Order ID</th><th>User ID</th><th>Date</th><th>Total (Rs)</th><th>Status</th><th>Items</th></tr>
<?php foreach ($orders as $o): ?>
<tr>
<td><?= (int)$o['order_id'] ?></td><td><?= (int)$o['user_id'] ?></td><td><?= esc($o['order_date']) ?></td><td>Rs <?= number_format((float)$o['total_price'], 2) ?></td>
<td><form method="post" action="update_order_status.php"><?= csrf_input() ?><input type="hidden" name="order_id" value="<?= (int)$o['order_id'] ?>"><select name="status" onchange="this.form.submit()"><?php foreach ($statuses as $status): ?><option value="<?= esc($status) ?>" <?= $status === $o['status'] ? 'selected' : '' ?>><?= esc($status) ?></option><?php endforeach; ?></select></form></td>
<td><a class="admin-link" href="admin_order_items.php?order_id=<?= (int)$o['order_id'] ?>">View Items</a></td>
</tr>
<?php endforeach; ?>
</table>
</body></html>

