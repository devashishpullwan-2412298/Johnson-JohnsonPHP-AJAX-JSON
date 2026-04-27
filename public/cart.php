<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();
$pdo = getPDO();
$user_id = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT c.cart_id, c.medicine_id, c.quantity, m.name, m.price FROM cart c INNER JOIN Medicines m ON c.medicine_id = m.medicine_id WHERE c.user_id = :uid');
$stmt->execute([':uid' => $user_id]);
$items = $stmt->fetchAll();
$total = 0.0;
$error = flash_message('flash_error');
$success = flash_message('flash_success');
?>
<!DOCTYPE html>
<html>
<head>
<title>Your Cart</title>
<link rel="stylesheet" href="../assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="page-container" style="max-width:900px;margin:40px auto;background:#fff;padding:30px;border-radius:10px;">
<h2>Your Cart</h2>
<?php if ($error): ?><p class="alert-inline error"><?= esc($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="alert-inline success"><?= esc($success) ?></p><?php endif; ?>
<?php if (empty($items)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
<table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
    <tr style="background:#2d7d46; color:white;">
        <th style="padding:10px; text-align:left;">Medicine</th><th style="padding:10px;">Qty</th><th style="padding:10px;">Price</th><th style="padding:10px;">Subtotal</th><th style="padding:10px;">Action</th>
    </tr>
    <?php foreach ($items as $row): $subtotal = (float)$row['price'] * (int)$row['quantity']; $total += $subtotal; ?>
    <tr style="border-bottom:1px solid #ddd;">
        <td style="padding:10px;"><?= esc($row['name']) ?></td>
        <td style="padding:10px; text-align:center;"><?= (int)$row['quantity'] ?></td>
        <td style="padding:10px;">Rs <?= number_format((float)$row['price'], 2) ?></td>
        <td style="padding:10px;">Rs <?= number_format($subtotal, 2) ?></td>
        <td style="padding:10px;">
            <form method="POST" action="cart_remove.php" style="display:inline;">
                <?= csrf_input() ?>
                <input type="hidden" name="id" value="<?= (int)$row['cart_id'] ?>">
                <button type="submit" style="color:#d9534f; font-weight:bold; background:none; border:none; cursor:pointer;">Remove</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<h3>Total: Rs <?= number_format($total, 2) ?></h3>
<a href="checkout.php" style="padding:12px 20px; background:#2d7d46; color:white; border-radius:6px; text-decoration:none;">Proceed to Checkout
</a>
<?php endif; ?>
</div>
</body>
</html>
<?php include __DIR__ . '/../src/templates/footer.php'; ?>