<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();

$stmt = getPDO()->query('SELECT medicine_id, name, description, category, price, stock, prescription_needed FROM Medicines WHERE is_deleted = 0 ORDER BY name ASC');
$medicines = $stmt->fetchAll();
$prescription_count = 0;
$image_dir = __DIR__ . '/../assets/images/medicines/';
$image_base = '../assets/images/medicines/';

foreach ($medicines as $medicine) {
    if ((int) $medicine['prescription_needed'] === 1) {
        $prescription_count++;
    }
}

function medicine_image_path(array $medicine, string $imageDir, string $imageBase): ?string {
    $id = (int) $medicine['medicine_id'];
    $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', trim((string) $medicine['name'])));
    $slug = trim($slug, '-');
    $candidates = [
        'medicine-' . $id . '.jpg',
        'medicine-' . $id . '.png',
        $slug . '.jpg',
        $slug . '.png',
    ];

    foreach ($candidates as $file) {
        if ($file !== '' && file_exists($imageDir . $file)) {
            return $imageBase . $file;
        }
    }

    return null;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="page-container customer-shell">
    <section class="customer-hero">
        <div class="hero-copy">
            <p class="eyebrow">Pharmacy Storefront</p>
            <h1>Available Medicines</h1>
            <p class="muted">Browse all medicines in one place, check stock quickly, and add the ones you need to your cart without leaving the page.</p>
        </div>
        <div class="hero-stats">
            <div class="stat-chip">
                <strong><?= count($medicines) ?></strong>
                <span>Medicines listed</span>
            </div>
            <div class="stat-chip">
                <strong><?= $prescription_count ?></strong>
                <span>Prescription items</span>
            </div>
        </div>
    </section>

    <?php if (empty($medicines)): ?>
        <section class="surface-card empty-state">
            <h2>No medicines available</h2>
            <p class="muted">The catalogue is empty right now. Please check again shortly.</p>
        </section>
    <?php else: ?>
        <section class="catalog-grid catalog-grid-wide">
            <?php foreach ($medicines as $medicine): ?>
                <?php
                $stock = (int) $medicine['stock'];
                $stock_class = 'stock-chip';
                if ($stock <= 10) {
                    $stock_class = 'stock-chip low';
                }
                $image = medicine_image_path($medicine, $image_dir, $image_base);
                $initials = strtoupper(substr((string) $medicine['name'], 0, 2));
                ?>
                <article class="catalog-card catalog-card-rich">
                    <?php if ($image !== null): ?>
                        <div class="catalog-image-wrap">
                            <img class="catalog-image" src="<?= esc($image) ?>" alt="<?= esc($medicine['name']) ?>">
                        </div>
                    <?php else: ?>
                        <div class="catalog-image-wrap placeholder">
                            <div class="catalog-image-fallback"><?= esc($initials) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="catalog-top">
                        <div>
                            <h3><?= esc($medicine['name']) ?></h3>
                            <p class="muted"><?= esc($medicine['description'] ?: 'No description available.') ?></p>
                        </div>
                        <?php if ((int) $medicine['prescription_needed'] === 1): ?>
                            <span class="status-pill pending">Prescription</span>
                        <?php endif; ?>
                    </div>
                    <div class="catalog-meta">
                        <span class="pill"><?= esc($medicine['category'] ?: 'General') ?></span>
                        <span class="<?= $stock_class ?>">Stock: <?= $stock ?></span>
                    </div>
                    <div class="catalog-price">Rs <?= number_format((float) $medicine['price'], 2) ?></div>
                    <?php if ($stock > 0): ?>
                        <form method="POST" action="add_to_cart.php">
                            <?= csrf_input() ?>
                            <input type="hidden" name="medicine_id" value="<?= (int) $medicine['medicine_id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="button">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <button class="button disabled" disabled>Out of Stock</button>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</div>
</body>
</html>
<?php include __DIR__ . '/../src/templates/footer.php'; ?>