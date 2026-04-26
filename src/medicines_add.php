<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/config.php';


if ($_SESSION['role'] !== 'pharmacist') {
    die("Access denied");
}

$name = trim($_POST['name'] ?? '');
$price = floatval($_POST['price'] ?? 0);

if ($name === '' || $price <= 0) {
    die("Invalid input");
}

$db = getPDO();
$stmt = $db->prepare("INSERT INTO medicines (name, price) VALUES (:n, :p)");
$stmt->execute([':n' => $name, ':p' => $price]);

header("Location: ../public/medicines.php");
exit;