<?php
// src/templates/header.php
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Online Pharmacy</title>
  <link rel="stylesheet" href="/online_pharmacy_full/assets/style.css">
</head>
<body>
<header>
  <nav>
    <a href="index.html">Home</a>
    <a href="medicines.php">Medicines</a>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="cart.php">Cart</a>
      <a href="logout.php">Logout</a>
      <?php if($_SESSION['role'] === 'admin'): ?>
        <a href="admin/add_medicine.php">Admin</a>
      <?php endif; ?>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main>