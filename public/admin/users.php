<?php
require_once __DIR__ . '/../../src/config.php';
requireRole('admin');
$users = getPDO()->query('SELECT user_id, name, email, role, created_at FROM Users ORDER BY user_id DESC')->fetchAll();
?><!DOCTYPE html><html><head><title>Users</title><link rel="stylesheet" href="../../assets/css/styles.css"></head><body><?php include __DIR__ . '/../navbar.php'; ?><h2>Users</h2><table border="1" cellpadding="10"><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th>Action</th></tr><?php foreach ($users as $u): ?><tr><td><?= (int)$u['user_id'] ?></td><td><?= esc($u['name']) ?></td><td><?= esc($u['email']) ?></td><td><?= esc($u['role']) ?></td><td><?= esc($u['created_at']) ?></td><td><a href="users_edit.php?id=<?= (int)$u['user_id'] ?>">Edit</a></td></tr><?php endforeach; ?></table></body></html>
