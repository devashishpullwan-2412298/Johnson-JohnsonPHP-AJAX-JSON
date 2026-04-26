<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = null;
if ($id) { $stmt = getPDO()->prepare('SELECT user_id, name, email, phone, role FROM Users WHERE user_id = ?'); $stmt->execute([$id]); $user = $stmt->fetch(); }
?><!DOCTYPE html><html><head><title>Edit User</title><link rel="stylesheet" href="../../assets/css/styles.css"></head><body><?php include __DIR__ . '/../navbar.php'; ?><h2>Edit User</h2><form method="POST" action="user_save.php"><?= csrf_input() ?><input type="hidden" name="user_id" value="<?= (int)($user['user_id'] ?? 0) ?>"><input type="text" name="name" value="<?= esc($user['name'] ?? '') ?>" required><input type="email" name="email" value="<?= esc($user['email'] ?? '') ?>" required><input type="text" name="phone" value="<?= esc($user['phone'] ?? '') ?>"><select name="role"><option value="customer" <?= (($user['role'] ?? '')==='customer')?'selected':'' ?>>customer</option><option value="pharmacist" <?= (($user['role'] ?? '')==='pharmacist')?'selected':'' ?>>pharmacist</option><option value="admin" <?= (($user['role'] ?? '')==='admin')?'selected':'' ?>>admin</option></select><button type="submit">Save</button></form></body></html>
