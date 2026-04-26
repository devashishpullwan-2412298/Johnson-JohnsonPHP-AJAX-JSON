<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');

$display_name = strip_tags(trim($_SESSION['name'] ?? ''));
if ($display_name === '') {
    $display_name = 'Admin';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . '/../navbar.php'; ?>
<style>
.dashboard-shell { max-width:1000px; margin:32px auto; padding:0 18px 36px; }
.dashboard-hero { background:linear-gradient(135deg, #f7fbf8, #e8f6ec); border:1px solid #d8e8de; border-radius:18px; padding:28px; box-shadow:0 18px 35px rgba(31, 90, 52, 0.08); }
.dashboard-hero h2 { margin:0 0 10px; font-size:2rem; color:#173f2a; }
.dashboard-hero p { margin:0; color:#56705e; line-height:1.6; }
.dashboard-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px; margin-top:22px; }
.dashboard-card { background:#fff; border:1px solid #dbe9e0; border-radius:16px; padding:22px; box-shadow:0 12px 24px rgba(31, 90, 52, 0.08); }
.dashboard-card h3 { margin:0 0 10px; color:#173f2a; }
.dashboard-card p { margin:0 0 16px; color:#587061; }
.dashboard-card a { display:inline-block; padding:10px 14px; background:#2f7d4b; color:#fff; text-decoration:none; border-radius:10px; font-weight:600; }
</style>
<div class="dashboard-shell">
  <section class="dashboard-hero">
    <h2>Admin Dashboard</h2>
    <p>Hello, <strong><?= esc($display_name) ?></strong>. Use these links to manage products and check orders across the pharmacy system.</p>
  </section>
  <section class="dashboard-grid">
    <article class="dashboard-card">
      <h3>Manage Products</h3>
      <p>Review and edit the medicines shown in the catalogue.</p>
      <a href="admin_products.php">Open Products</a>
    </article>
    <article class="dashboard-card">
      <h3>Manage Orders</h3>
      <p>Check placed orders and follow the shopping flow from start to finish.</p>
      <a href="admin_orders.php">Open Orders</a>
    </article>
  </section>
</div>
</body>
</html>
