<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/json_catalog.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_die();
    $result = write_medicine_catalog(getPDO());
    echo json_encode([
        'action' => 'create',
        'ok' => $result['ok'],
        'errors' => $result['errors'],
        'payload' => $result['payload'],
        'schema_file' => basename(medicine_catalog_schema_file()),
        'json_file' => basename(medicine_catalog_file()),
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$result = read_medicine_catalog();
echo json_encode([
    'action' => 'consume',
    'ok' => $result['ok'],
    'errors' => $result['errors'],
    'payload' => $result['payload'],
    'schema_file' => basename(medicine_catalog_schema_file()),
    'json_file' => basename(medicine_catalog_file()),
], JSON_UNESCAPED_SLASHES);
