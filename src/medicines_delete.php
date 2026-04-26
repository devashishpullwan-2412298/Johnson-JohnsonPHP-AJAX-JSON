<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/config.php';


if ($_SESSION['role'] !== 'pharmacist') {
    die("Access denied.");
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid ID");
}

$db = getPDO();
$stmt = $db->prepare("DELETE FROM medicines WHERE id = :id");
$stmt->execute([':id' => $id]);

header("Location: ../public/medicines.php");
exit;
