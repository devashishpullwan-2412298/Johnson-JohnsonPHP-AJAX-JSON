<?php
require_once __DIR__ . '/../src/config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Online Pharmacy</title>

  <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>

<?php include __DIR__ . '/navbar.php'; ?>

<!-- ANIMATED BACKGROUND -->
<div class="bg-animation">
  <span></span>
  <span></span>
  <span></span>
  <span></span>
</div>

<main class="container">
<!-- HERO -->
<section class="hero modern-hero">

  <h1>Johnson & Johnson ⚕️</h1>

  <p class="hero-subtitle">
    Safe, fast and reliable medicine ordering system.
    Manage prescriptions, orders and delivery in one place.
  </p>

  <div class="hero-buttons">
    <a href="products.php" class="button primary">Browse Medicines</a>
    <a href="cart.php" class="button secondary">View Cart</a>
  </div>

</section>

<!-- FEATURES -->
<section class="card-container features">

  <div class="card feature-card reveal">
    <div class="feature-img-wrapper">
      <img src="../assets/animations/medicine.gif" alt="Medicines">
    </div>
    <h3>Medicines</h3>
    <p class="muted">Browse a wide range of approved medicines.</p>
  </div>

  <div class="card feature-card reveal">
    <div class="feature-img-wrapper">
      <img src="../assets/animations/delivery.gif" alt="Delivery">
    </div>
    <h3>Fast Delivery</h3>
    <p class="muted">Get your medicines delivered safely.</p>
  </div>

  <div class="card feature-card reveal">
    <div class="feature-img-wrapper">
      <img src="../assets/animations/prescription.gif" alt="Prescription">
    </div>
    <h3>Prescription Upload</h3>
    <p class="muted">Upload prescriptions easily for approval.</p>
  </div>

</section>

</main>

<!-- SCROLL ANIMATION SCRIPT -->
<script>
const reveals = document.querySelectorAll('.reveal');

window.addEventListener('scroll', () => {
  const triggerBottom = window.innerHeight * 0.85;

  reveals.forEach(el => {
    const top = el.getBoundingClientRect().top;

    if (top < triggerBottom) {
      el.classList.add('active');
    }
  });
});
</script>

</body>
</html>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>