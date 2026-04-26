<?php
require_once "config/db.php";

if(!isset($_GET['id'])){
    die("Invalid request");
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../public/cart.php");
exit();
?>