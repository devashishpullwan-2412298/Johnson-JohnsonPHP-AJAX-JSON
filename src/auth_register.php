<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}
verify_csrf_or_die();
$name = trim((string)($_POST['fullname'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$role = 'customer';
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    $_SESSION['flash_error'] = 'Invalid input';
    redirect_to('register.php');
}
$db = getPDO();
$stmt = $db->prepare('SELECT user_id FROM Users WHERE email = :email');
$stmt->execute([':email' => $email]);
if ($stmt->fetch()) {
    $_SESSION['flash_error'] = 'Email already exists';
    redirect_to('register.php');
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare('INSERT INTO Users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, NOW())');
$stmt->execute([':name' => $name, ':email' => $email, ':password' => $hash, ':role' => $role]);
$_SESSION['flash_success'] = 'Registration successful. Please log in.';
redirect_to('login.php');
