<?php
require_once '../../src/db.php';
require_once '../../includes/admin/auth_admin.php';

if (!isset($_GET['id'])) {
    die("User ID missing");
}

$stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->execute([$_GET['id']]);

header("Location: users.php?msg=deleted");
exit;