<?php
require_once '../../src/db.php';
require_once '../../includes/admin/auth_admin.php';

$name = $_POST['name'];
$email = $_POST['email'];
$role = $_POST['role'];
$password = $_POST['password'];

if (isset($_POST['user_id'])) {
    // UPDATE
    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET name=?, email=?, role=?, password=? WHERE user_id=?");
        $stmt->execute([$name, $email, $role, $hash, $_POST['user_id']]);
    } else {
        $stmt = $db->prepare("UPDATE users SET name=?, email=?, role=? WHERE user_id=?");
        $stmt->execute([$name, $email, $role, $_POST['user_id']]);
    }

} else {
    // INSERT
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at)
                          VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $hash, $role]);
}

header("Location: users.php?msg=saved");
exit;