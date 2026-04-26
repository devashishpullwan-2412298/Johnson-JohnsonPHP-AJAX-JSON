<?php
require_once '../../src/db.php';
require_once '../../includes/admin/auth_admin.php';
require_once '../../includes/admin/admin_header.php';

if (!isset($_GET['id'])) {
    die("Order ID missing");
}

$order_id = $_GET['id'];

$stmt = $db->prepare("SELECT o.*, u.name, u.email
                      FROM orders o
                      JOIN users u ON o.user_id = u.user_id
                      WHERE o.order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found");
}

$itemStmt = $db->prepare("SELECT p.name, oi.quantity, oi.price
                          FROM order_items oi
                          JOIN products p ON p.product_id = oi.product_id
                          WHERE oi.order_id = ?");
itemStmt->execute([$order_id]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Order #<?= $order_id ?></h2>

<p><strong>User:</strong> <?= htmlspecialchars($order['name']) ?> (<?= $order['email'] ?>)</p>
<p><strong>Status:</strong> <?= $order['status'] ?></p>
<p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>

<h3>Items</h3>
<table border="1" cellpadding="8">
<tr><th>Product</th><th>Qty</th><th>Price</th></tr>
<?php foreach ($items as $i): ?>
<tr>
    <td><?= htmlspecialchars($i['name']) ?></td>
    <td><?= $i['quantity'] ?></td>
    <td>$<?= number_format($i['price'], 2) ?></td>
</tr>
<?php endforeach; ?>
</table>

<h3>Update Status</h3>
<form action="order_status_update.php" method="post">
    <input type="hidden" name="order_id" value="<?= $order_id ?>">

    <select name="status" required>
        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>

    <button type="submit">Update</button>
</form>

<?php require_once '../../includes/admin/admin_footer.php'; ?>