<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
$pdo = getPDO();
$products = $pdo->query('SELECT medicine_id, name, price, stock, is_deleted FROM Medicines ORDER BY medicine_id DESC')->fetchAll();
?>
<!DOCTYPE html><html><head><title>Admin Products</title><link rel="stylesheet" href="../../assets/css/styles.css"></head><body>
<?php include __DIR__ . '/../navbar.php'; ?>
<h2 class="admin-section-title">Manage Products</h2>
<a class="admin-action-button" href="admin_products_edit.php">Add New Product</a>
<table class="admin-table"><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($products as $p): ?>
<tr style="<?= (int)$p['is_deleted'] ? 'opacity:0.5;' : '' ?>">
<td><?= (int)$p['medicine_id'] ?></td><td><?= esc($p['name']) ?></td><td>Rs <?= number_format((float)$p['price'], 2) ?></td><td><?= (int)$p['stock'] ?></td><td><?= (int)$p['is_deleted'] ? 'Deleted' : 'Active' ?></td>
<td>
<a class="admin-link" href="admin_products_edit.php?id=<?= (int)$p['medicine_id'] ?>">Edit</a> |
<form method="POST" action="admin_products_toggle.php" style="display:inline;">
<?= csrf_input() ?>
<input type="hidden" name="id" value="<?= (int)$p['medicine_id'] ?>">
<?php if ((int)$p['is_deleted']): ?>
<input type="hidden" name="action" value="restore"><button type="submit">Restore</button>
<?php else: ?>
<input type="hidden" name="action" value="delete"><button type="submit" onclick="return confirm('Soft delete this product?');">Delete</button>
<?php endif; ?>
</form>
</td></tr>
<?php endforeach; ?>
</table>
</body></html>


