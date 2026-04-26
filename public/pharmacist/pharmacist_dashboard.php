<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('pharmacist');

$prescriptions = getPDO()->query('SELECT p.prescription_id, p.order_id, p.file_path, p.status, u.name FROM Prescriptions p JOIN Users u ON u.user_id = p.user_id ORDER BY p.prescription_id DESC')->fetchAll();
$display_name = strip_tags(trim($_SESSION['name'] ?? ''));
if ($display_name === '') {
    $display_name = 'Pharmacist';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prescription Approvals</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . '/../navbar.php'; ?>
<style>
.dashboard-shell { max-width:1100px; margin:32px auto; padding:0 18px 36px; }
.dashboard-hero { background:linear-gradient(135deg, #f7fbf8, #e8f6ec); border:1px solid #d8e8de; border-radius:18px; padding:28px; box-shadow:0 18px 35px rgba(31, 90, 52, 0.08); }
.dashboard-hero h2 { margin:0 0 10px; color:#173f2a; font-size:2rem; }
.dashboard-hero p { margin:0; color:#587061; line-height:1.6; }
.dashboard-table { width:100%; border-collapse:collapse; margin-top:22px; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 14px 28px rgba(31, 90, 52, 0.08); }
.dashboard-table th, .dashboard-table td { padding:14px 16px; border-bottom:1px solid #e6efe9; text-align:left; }
.dashboard-table th { background:#edf7f0; color:#173f2a; }
.status-pill { display:inline-block; padding:6px 10px; border-radius:999px; background:#eef8f1; color:#2f7d4b; font-weight:600; text-transform:capitalize; }
.action-form { display:inline-block; margin-right:8px; }
.action-form button, .view-link { display:inline-block; padding:8px 12px; border-radius:10px; text-decoration:none; font-weight:600; border:none; cursor:pointer; }
.view-link { background:#edf7f0; color:#1f5a34; }
.approve-btn { background:#1f7a42; color:#fff; }
.reject-btn { background:#a93d3d; color:#fff; }
</style>
<div class="dashboard-shell">
  <section class="dashboard-hero">
    <h2>Prescription Dashboard</h2>
    <p>Hello, <strong><?= esc($display_name) ?></strong>. Review uploaded prescriptions, open the file, and approve or reject the request here.</p>
  </section>
  <table class="dashboard-table">
    <tr><th>Order</th><th>User</th><th>Prescription</th><th>Status</th><th>Action</th></tr>
    <?php foreach ($prescriptions as $prescription): ?>
    <tr>
      <td>#<?= (int) $prescription['order_id'] ?></td>
      <td><?= esc(strip_tags(trim($prescription['name']))) ?></td>
      <td><a class="view-link" href="<?= esc('../../' . ltrim($prescription['file_path'], '/')) ?>" target="_blank">View File</a></td>
      <td><span class="status-pill"><?= esc($prescription['status']) ?></span></td>
      <td>
        <?php if ($prescription['status'] === 'pending'): ?>
          <form class="action-form" method="POST" action="approve_prescription.php">
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= (int) $prescription['prescription_id'] ?>">
            <input type="hidden" name="action" value="approve">
            <button class="approve-btn" type="submit">Approve</button>
          </form>
          <form class="action-form" method="POST" action="approve_prescription.php">
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= (int) $prescription['prescription_id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button class="reject-btn" type="submit">Reject</button>
          </form>
        <?php else: ?>
          <span style="color:#587061;">No action needed</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>
