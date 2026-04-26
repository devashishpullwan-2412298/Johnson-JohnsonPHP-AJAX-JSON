<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/config.php';


if ($_SESSION['role'] !== 'pharmacist') {
    die("Access denied");
}

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$price = floatval($_POST['price'] ?? 0);

if ($id <= 0 || $name === '' || $price <= 0) {
    die("Invalid input");
}

$db = getPDO();
$stmt = $db->prepare("UPDATE medicines SET name = :n, price = :p WHERE id = :id");
$stmt->execute([
    ':n' => $name,
    ':p' => $price,
    ':id' => $id
]);

header("Location: ../public/medicines.php");
exit;
