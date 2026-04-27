<?php
require_once __DIR__ . '/../src/config.php';

$logged_in = isLoggedIn();
$role = $_SESSION['role'] ?? '';
$base = rtrim(app_base(), '/');
$cart_count = 0;
$display_name = 'User';

if ($logged_in) {
    try {
        $stmt = getPDO()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $cart_count = (int) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cart_count = 0;
    }

    $name = strip_tags(trim($_SESSION['name'] ?? ''));
    if ($name !== '') {
        $display_name = $name;
    }
}
?>
<style>
.navbar { background:linear-gradient(135deg, #1f5a34, #2f7d4b); padding:14px 18px; color:#fff; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; border-radius:14px; box-shadow:0 10px 25px rgba(31, 90, 52, 0.18); margin:18px 18px 0; }
.navbar a { color:#fff; text-decoration:none; margin-right:10px; font-weight:600; padding:8px 12px; border-radius:999px; transition:background .2s ease; }
.navbar a:hover { background:rgba(255,255,255,0.14); }
.nav-left, .nav-right { display:flex; align-items:center; flex-wrap:wrap; gap:4px; }
.nav-pill { background:rgba(255,255,255,0.14); border-radius:999px; padding:8px 14px; font-weight:600; }
</style>
<div class="navbar">

  <div class="nav-left">
    
    <div class="logo">💊 Johnson & Johnson</div>

    <a href="<?= esc($base . '/index.php') ?>">Home</a>
    <a href="<?= esc($base . '/products.php') ?>">Products</a>

    <a href="<?= esc($base . '/cart.php') ?>">
      Cart <span class="cart-badge"><?= $cart_count ?></span>
    </a>

    <?php if ($logged_in): ?>
      <a href="<?= esc($base . '/my_orders.php') ?>">My Orders</a>
      <a href="<?= esc($base . '/profile.php') ?>">Profile</a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
      <a href="<?= esc($base . '/admin/admin_dashboard.php') ?>">Admin</a>
    <?php endif; ?>

    <?php if ($role === 'pharmacist'): ?>
      <a href="<?= esc($base . '/pharmacist/pharmacist_dashboard.php') ?>">Pharmacist</a>
    <?php endif; ?>

  </div>

  <div class="nav-right">
    <?php if ($logged_in): ?>
      <span class="nav-pill">Hi, <?= esc($display_name) ?></span>
      <a href="<?= esc($base . '/logout.php') ?>" class="logout-btn">Logout</a>
    <?php else: ?>
      <a href="<?= esc($base . '/login.php') ?>" class="login-btn">Login</a>
      <a href="<?= esc($base . '/register.php') ?>" class="register-btn">Register</a>
    <?php endif; ?>
  </div>

</div>