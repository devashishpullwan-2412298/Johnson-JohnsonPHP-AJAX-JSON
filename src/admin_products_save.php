<?php
require_once __DIR__ . "/common.php";
require_once __DIR__ . "/database.php";


// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.html");
    exit;
}

// Ensure POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed");
}

$db = getPDO();

$id     = $_POST["product_id"] ?? null;
$name   = trim($_POST["name"] ?? "");
$price  = $_POST["price"] ?? "";
$stock  = $_POST["stock"] ?? "";

// Basic validation
if ($name === "" || !is_numeric($price) || !is_numeric($stock)) {
    die("Invalid input");
}

// If product_id exists → update
if (!empty($id)) {

    $stmt = $db->prepare("
        UPDATE products 
        SET name = :name, price = :price, stock = :stock 
        WHERE product_id = :id
    ");

    $stmt->execute([
        ":name" => $name,
        ":price" => $price,
        ":stock" => $stock,
        ":id" => $id
    ]);

} else {
    // Otherwise → insert new product
    $stmt = $db->prepare("
        INSERT INTO products (name, price, stock, created_at)
        VALUES (:name, :price, :stock, NOW())
    ");

    $stmt->execute([
        ":name" => $name,
        ":price" => $price,
        ":stock" => $stock
    ]);
}

// Redirect back to products list
header("Location: ../public/admin/admin_products.php");
exit;