<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/config.php';
$db = getPDO();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if($q !== ''){
    $stmt = $db->prepare('SELECT id,name,category,price,stock FROM medicines WHERE name LIKE :q OR category LIKE :q');
    $stmt->execute([':q'=>"%{$q}%"]);
} else {
    $stmt = $db->query('SELECT id,name,category,price,stock FROM medicines');
}
$meds = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Medicines</title><link rel="stylesheet" href="../assets/css/styles.css"></head><body>
<h2>Medicines</h2>
<form method="get" action="medicines.php">
  <input name="q" placeholder="Search by name or category" value="<?php echo htmlspecialchars($q); ?>">
  <button>Search</button>
</form>

<table><thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead><tbody>
<?php foreach($meds as $m): ?>
  <tr>
    <td><?php echo htmlspecialchars($m['name']); ?></td>
    <td><?php echo htmlspecialchars($m['category']); ?></td>
    <td><?php echo number_format($m['price'],2); ?></td>
    <td><?php echo (int)$m['stock']; ?></td>
    <td>
      <a href="medicines_view.php?id=<?php echo (int)$m['id']; ?>">View</a>
      <a href="medicines_edit.php?id=<?php echo (int)$m['id']; ?>">Edit</a>
      <a href="medicines_delete.php?id=<?php echo (int)$m['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
    </td>
  </tr>
<?php endforeach; ?>
</tbody></table>
<p><a href="medicines_add.php" class="button">Add Medicine</a></p>
<p><a href="../src/cart_view.php" class="button">View Cart</a></p>
</body></html>
