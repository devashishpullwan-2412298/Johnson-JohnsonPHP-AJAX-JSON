<?php
require_once __DIR__ . '/db.php';

// REQUIRE LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// MUST BE POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method not allowed");
}

$cart_id = $_POST['cart_id'] ?? null;
$action  = $_POST['action'] ?? null;

if (!$cart_id || !$action) {
    exit("Invalid request");
}

// SECURITY CHECK — ensure this cart item belongs to the logged-in user
$check = $db->prepare("SELECT quantity FROM cart WHERE cart_id = :cid AND user_id = :uid");
$check->execute([':cid' => $cart_id, ':uid' => $user_id]);
$item = $check->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    exit("Unauthorized attempt");
}

$quantity = (int)$item['quantity'];

// PROCESS ACTIONS
switch ($action) {

    case "increase":
        $stmt = $db->prepare("UPDATE cart SET quantity = quantity + 1 WHERE cart_id = :cid");
        $stmt->execute([':cid' => $cart_id]);
        break;

    case "decrease":
        if ($quantity > 1) {
            $stmt = $db->prepare("UPDATE cart SET quantity = quantity - 1 WHERE cart_id = :cid");
            $stmt->execute([':cid' => $cart_id]);
        } else {
            // qty would become 0 → remove item
            $stmt = $db->prepare("DELETE FROM cart WHERE cart_id = :cid");
            $stmt->execute([':cid' => $cart_id]);
        }
        break;

    case "remove":
        $stmt = $db->prepare("DELETE FROM cart WHERE cart_id = :cid");
        $stmt->execute([':cid' => $cart_id]);
        break;

    default:
        exit("Invalid action");
}

header("Location: ../public/cart.php");
exit;