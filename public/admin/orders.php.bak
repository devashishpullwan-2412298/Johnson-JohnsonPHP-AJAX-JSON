<?php
require_once '../../src/db.php';
require_once '../../includes/admin/auth_admin.php';
require_once '../../includes/admin/admin_header.php';

$stmt = $db->query("SELECT o.order_id, o.user_id, u.name, o.total, o.status, o.created_at
                    FROM orders o 
                    JOIN users u ON u.user_id = o.user_id
                    ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Orders</h2>

<table border="1" cellpadding="8">
<tr>
    <th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th>
</tr>

<?php foreach ($orders as $o): ?>
<tr>
    <td><?= $o['order_id'] ?></td>
    <td><?= htmlspecialchars($o['name']) ?></td>
    <td>$<?= number_format($o['total'], 2) ?></td>
    <td><?= $o['status'] ?></td>
    <td><?= $o['created_at'] ?></td>
    <td><a href="order_view.php?id=<?= $o['order_id'] ?>">View</a></td>
</tr>
<?php endforeach; ?>
</table>

<?php require_once '../../includes/admin/admin_footer.php'; ?>