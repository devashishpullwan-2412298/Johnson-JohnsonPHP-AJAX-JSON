<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/config.php';
$cart = $_SESSION['cart'] ?? [];
?>
<!doctype html><html><head><meta charset="utf-8"><title>Cart</title><link rel="stylesheet" href="../assets/css/styles.css"></head><body>
<h2>Your Cart</h2>
<?php if(empty($cart)): ?>
  <p>Your cart is empty. <a href="medicines.php">Browse medicines</a></p>
<?php else: ?>
  <form method="post" action="checkout.php">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf(); ?>">
    <table><thead><tr><th>Name</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead><tbody>
    <?php $total = 0; foreach($cart as $item): $line = $item['price'] * $item['qty']; $total += $line; ?>
      <tr>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
        <td><?php echo (int)$item['qty']; ?></td>
        <td><?php echo number_format($item['price'],2); ?></td>
        <td><?php echo number_format($line,2); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
    <p>Total: <?php echo number_format($total,2); ?></p>

    <label>Upload prescription (required for prescription meds)
      <input type="file" name="prescription" form="checkoutForm" required>
    </label>

    <form id="checkoutForm" method="post" action="checkout.php" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf(); ?>">
      <label>Delivery address<textarea name="address" required></textarea></label>
      <button class="button" type="submit">Checkout</button>
    </form>
  </form>
<?php endif; ?>
</body></html>
