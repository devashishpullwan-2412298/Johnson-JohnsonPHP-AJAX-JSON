<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
$db = getPDO();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$product = null;
if ($id) {
    $stmt = $db->prepare('SELECT * FROM Medicines WHERE medicine_id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}
?>
<!DOCTYPE html><html><head><title><?= $id ? 'Edit Medicine' : 'Add Medicine' ?></title><link rel="stylesheet" href="../../assets/css/styles.css"></head><body>
<?php include __DIR__ . '/../navbar.php'; ?>
<h2><?= $id ? 'Edit Medicine' : 'Add Medicine' ?></h2>
<form method="post" action="admin_products_save.php">
    <?= csrf_input() ?>
    <input type="hidden" name="medicine_id" value="<?= (int)($product['medicine_id'] ?? 0) ?>">
    <input type="text" name="name" placeholder="Name" value="<?= esc($product['name'] ?? '') ?>" required>
    <textarea name="description" placeholder="Description" rows="4" required><?= esc($product['description'] ?? '') ?></textarea>
    <input type="text" name="category" placeholder="Category" value="<?= esc($product['category'] ?? '') ?>" required>
    <input type="number" step="0.01" min="0" name="price" placeholder="Price" value="<?= esc($product['price'] ?? '') ?>" required>
    <input type="number" min="0" name="stock" placeholder="Stock" value="<?= esc($product['stock'] ?? '') ?>" required>
    <label><input type="checkbox" name="prescription_needed" value="1" <?= !empty($product['prescription_needed']) ? 'checked' : '' ?>> Prescription needed</label>
    <button type="submit">Save</button>
</form>
</body></html>
