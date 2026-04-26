<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/config.php';
$db = getPDO();
$id = intval($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM medicines WHERE id = :id');
$stmt->execute([':id'=>$id]);
$m = $stmt->fetch();
if(!$m) die('Not found');
?>
<!doctype html><html><head><meta charset="utf-8"><title>View</title><link rel="stylesheet" href="../assets/css/styles.css"></head><body>
<h2><?php echo htmlspecialchars($m['name']); ?></h2>
<p class="muted">Category: <?php echo htmlspecialchars($m['category']); ?></p>
<p>Price: <?php echo number_format($m['price'],2); ?></p>
<p>Stock: <?php echo (int)$m['stock']; ?></p>

<form action="cart_add.php" method="post" novalidate>
  <input type="hidden" name="csrf_token" value="<?php echo generate_csrf(); ?>">
  <input type="hidden" name="medicine_id" value="<?php echo (int)$m['id']; ?>">
  <label>Quantity<input required name="qty" type="number" value="1" min="1" max="<?php echo (int)$m['stock']; ?>"></label>
  <button class="button">Add to cart</button>
</form>
<p><a href="medicines.php">Back</a></p>
</body></html>
