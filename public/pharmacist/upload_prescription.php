<?php
require_once __DIR__ . '/../../src/config.php';
requireLogin();
$pdo = getPDO();
$user_id = (int)$_SESSION['user_id'];
try {
    $order_id = get_int('order_id');
    get_prescription_upload_context($pdo, $order_id, $user_id);
} catch (Throwable $e) {
    $_SESSION['flash_error'] = $e instanceof RuntimeException ? $e->getMessage() : 'Unable to open that prescription upload.';
    redirect_to('my_orders.php');
}
?>
<!DOCTYPE html>
<html><head><title>Upload Prescription</title><link rel="stylesheet" href="../../assets/css/styles.css"></head><body>
<?php include __DIR__ . '/../navbar.php'; ?>
<h2>Upload Prescription</h2>
<p>Accepted formats: PDF, JPG, PNG. Maximum size: 5 MB.</p>
<form method="POST" action="upload_prescription_process.php" enctype="multipart/form-data">
    <?= csrf_input() ?>
    <input type="hidden" name="order_id" value="<?= (int)$order_id ?>">
    <input type="file" name="prescription" accept=".pdf,.png,.jpg,.jpeg" required>
    <button type="submit">Upload</button>
</form>
</body></html>
