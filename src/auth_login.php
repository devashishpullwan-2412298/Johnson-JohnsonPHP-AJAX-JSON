<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}
verify_csrf_or_die();
enforce_login_throttle();
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    exit('Enter a valid email and password.');
}
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT user_id, name, email, password, role FROM Users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password'])) {
    register_failed_login_attempt();
    exit('Invalid email or password.');
}
complete_login($user);
