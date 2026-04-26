<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/json_catalog.php';
requireLogin();

$pdo = getPDO();
$categories = $pdo->query(
    'SELECT DISTINCT COALESCE(category, "General") AS category
     FROM Medicines
     WHERE is_deleted = 0
     ORDER BY category ASC'
)->fetchAll(PDO::FETCH_COLUMN);
$phpCatalogState = read_medicine_catalog();
$phpCatalog = $phpCatalogState['payload']['medicines'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX and JSON Showcase</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="page-container showcase-shell">
    <section class="showcase-hero">
        <div>
            <p class="eyebrow">Frontend + Backend Demonstration</p>
            <h1>jQuery, AJAX, JSON, and JSON Schema</h1>
            <p class="muted">This page demonstrates event registration, asynchronous requests, JSON creation, JSON consumption in PHP and jQuery, and schema validation at both write time and read time.</p>
        </div>
        <div class="showcase-actions">
            <button type="button" id="load-medicines" class="button">Load Medicines with AJAX</button>
            <button type="button" id="create-json" class="button secondary">Create Validated JSON File</button>
            <button type="button" id="consume-json" class="button secondary">Consume JSON File</button>
        </div>
    </section>

    <section class="showcase-grid">
        <div class="showcase-panel">
            <h2>AJAX Filters</h2>
            <form id="medicine-filter-form">
                <?= csrf_input() ?>
                <label for="search-term">Search by name or description</label>
                <input id="search-term" name="search" type="text" placeholder="Try paracetamol">

                <label for="category-filter">Category</label>
                <select id="category-filter" name="category">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= esc((string) $category) ?>"><?= esc((string) $category) ?></option>
                    <?php endforeach; ?>
                </select>

                <label class="checkbox-row">
                    <input id="prescription-only" name="prescription_only" type="checkbox" value="1">
                    Prescription only
                </label>
            </form>
            <div id="ajax-status" class="status-box info">Use the filters or buttons above to trigger jQuery AJAX requests.</div>
            <div id="medicine-results" class="results-grid"></div>
        </div>

        <div class="showcase-panel">
            <h2>JSON Schema Validation</h2>
            <p class="muted">The JSON file is written to <code>data/medicine_catalog.json</code> and validated against <code>data/medicine_catalog.schema.json</code>.</p>
            <div id="json-status" class="status-box info">Schema validation results will appear here.</div>
            <pre id="json-preview" class="json-preview">No JSON loaded yet.</pre>
        </div>
    </section>

    <section class="showcase-panel">
        <h2>PHP Consuming the JSON File</h2>
        <p class="muted">This block is rendered server-side in PHP by loading the same JSON file and validating it again during consumption.</p>
        <div class="status-box <?= $phpCatalogState['ok'] ? 'success' : 'error' ?>">
            <?= $phpCatalogState['ok'] ? 'PHP successfully consumed a schema-valid JSON file.' : 'PHP detected JSON/schema issues while consuming the file.' ?>
            <?php if ($phpCatalogState['errors'] !== []): ?>
                <br><?= esc(implode(' | ', $phpCatalogState['errors'])) ?>
            <?php endif; ?>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($phpCatalog !== []): ?>
                <?php foreach ($phpCatalog as $medicine): ?>
                    <tr>
                        <td><?= (int) $medicine['medicine_id'] ?></td>
                        <td><?= esc($medicine['name']) ?></td>
                        <td><?= esc($medicine['category'] ?? 'General') ?></td>
                        <td>Rs <?= number_format((float) $medicine['price'], 2) ?></td>
                        <td><?= (int) $medicine['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">The JSON file is empty or has not been generated from the database yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>
<script src="../assets/js/scripts.js"></script>
</body>
</html>
