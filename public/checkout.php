<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();

$user_id = (int) $_SESSION['user_id'];
$stmt = getPDO()->prepare('SELECT c.cart_id, c.quantity, m.medicine_id, m.name, m.price, m.description, m.prescription_needed FROM cart c JOIN Medicines m ON c.medicine_id = m.medicine_id WHERE c.user_id = :uid');
$stmt->execute([':uid' => $user_id]);
$items = $stmt->fetchAll();
$error = flash_message('flash_error');
$total_price = 0;
$requires_prescription = false;

foreach ($items as $item) {
    $total_price += (float) $item['price'] * (int) $item['quantity'];
    if ((int) $item['prescription_needed'] === 1) {
        $requires_prescription = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="page-container customer-shell">
    <section class="customer-hero">
        <div class="hero-copy">
            <p class="eyebrow">Final Step</p>
            <h1>Checkout</h1>
            <p class="muted">Confirm your delivery details, choose a payment option, and upload a prescription when your order includes restricted medicine.</p>
        </div>
        <div class="hero-stats">
            <div class="stat-chip">
                <strong><?= count($items) ?></strong>
                <span>Cart items</span>
            </div>
            <div class="stat-chip">
                <strong>Rs <?= number_format($total_price, 2) ?></strong>
                <span>Order total</span>
            </div>
        </div>
    </section>

    <?php if ($error): ?>
        <div class="alert-inline error"><?= esc($error) ?></div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <section class="surface-card empty-state">
            <h2>Your cart is empty</h2>
            <p class="muted">Add a few medicines first, then come back here to complete your order.</p>
        </section>
    <?php else: ?>
        <div class="checkout-grid">
            <section class="summary-card">
                <h2>Order Summary</h2>
                <table class="checkout-summary-table">
                    <tr><th>Medicine</th><th>Qty</th><th>Subtotal</th></tr>
                    <?php foreach ($items as $item): ?>
                        <?php $subtotal = (float) $item['price'] * (int) $item['quantity']; ?>
                        <tr>
                            <td>
                                <strong><?= esc($item['name']) ?></strong><br>
                                <span class="muted">Rs <?= number_format((float) $item['price'], 2) ?> each</span>
                            </td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td>Rs <?= number_format($subtotal, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div class="checkout-total">
                    <span>Total</span>
                    <span>Rs <?= number_format($total_price, 2) ?></span>
                </div>
            </section>

            <section class="form-card">
                <h2>Delivery &amp; Payment</h2>
                <form class="form-stack" action="checkout_process.php" method="POST" enctype="multipart/form-data">
                    <?= csrf_input() ?>
                    <div>
                        <label for="address">Delivery Address</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>
                    <div>
                        <label for="phone">Phone Number</label>
                        <input id="phone" type="text" name="phone" required>
                    </div>
                    <div>
                        <label for="order_type">Order Type</label>
                        <select id="order_type" name="order_type" required>
                            <option value="Pickup">Pickup</option>
                            <option value="Delivery">Delivery</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" required onchange="toggleCardFields()">
                            <option value="cod">Cash on Delivery</option>
                            <option value="mock_card">Card (Simulated)</option>
                        </select>
                    </div>
                    <div id="card_fields" class="surface-card" style="display:none; padding:1rem;">
                        <h3 class="profile-section-title">Card Details</h3>
                        <div class="form-stack">
                            <div>
                                <label>Card Number</label>
                                <input type="text" placeholder="1234 5678 9012 3456">
                            </div>
                            <div>
                                <label>Expiry Date</label>
                                <input type="text" placeholder="MM/YY">
                            </div>
                            <div>
                                <label>CVV</label>
                                <input type="password" placeholder="123">
                            </div>
                        </div>
                        <p class="form-note">This is a simulated payment step. No real card data is processed by the project.</p>
                    </div>
                    <?php if ($requires_prescription): ?>
                        <div class="upload-callout">
                            <strong>Prescription required.</strong>
                            <p style="margin:.45rem 0 0;">This order includes prescription medicine. Please upload a PDF, JPG, or PNG file before placing the order.</p>
                        </div>
                        <div>
                            <label for="prescription_file">Prescription File</label>
                            <input id="prescription_file" type="file" name="prescription_file" accept=".pdf,.png,.jpg,.jpeg" required>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="button">Place Order</button>
                </form>
            </section>
        </div>
    <?php endif; ?>
</div>
<script>
function toggleCardFields() {
    var method = document.getElementById('payment_method').value;
    var cardFields = document.getElementById('card_fields');

    if (method === 'mock_card') {
        cardFields.style.display = 'block';
    } else {
        cardFields.style.display = 'none';
    }
}
</script>
</body>
</html>
<?php include __DIR__ . '/../src/templates/footer.php'; ?>