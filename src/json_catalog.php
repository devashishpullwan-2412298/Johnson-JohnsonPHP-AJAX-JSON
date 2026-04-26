<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/json_schema_validator.php';

function medicine_catalog_file(): string
{
    return dirname(__DIR__) . '/data/medicine_catalog.json';
}

function medicine_catalog_schema_file(): string
{
    return dirname(__DIR__) . '/data/medicine_catalog.schema.json';
}

function medicine_catalog_schema(): array
{
    static $schema;

    if ($schema === null) {
        $schemaJson = file_get_contents(medicine_catalog_schema_file());
        $schema = json_decode($schemaJson ?: '{}', true) ?: [];
    }

    return $schema;
}

function build_medicine_catalog_payload(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT medicine_id, name, description, category, price, stock, prescription_needed
         FROM Medicines
         WHERE is_deleted = 0
         ORDER BY name ASC'
    );

    $rows = array_map(
        static function (array $medicine): array {
            return [
                'medicine_id' => (int) $medicine['medicine_id'],
                'name' => (string) $medicine['name'],
                'description' => $medicine['description'] !== null ? (string) $medicine['description'] : null,
                'category' => $medicine['category'] !== null ? (string) $medicine['category'] : null,
                'price' => (float) $medicine['price'],
                'stock' => (int) $medicine['stock'],
                'prescription_needed' => (bool) $medicine['prescription_needed'],
            ];
        },
        $stmt->fetchAll()
    );

    return [
        'generated_at' => date(DATE_ATOM),
        'source' => 'database-sync',
        'total' => count($rows),
        'medicines' => $rows,
    ];
}

function validate_medicine_catalog(array $payload): array
{
    return json_schema_validate($payload, medicine_catalog_schema());
}

function write_medicine_catalog(PDO $pdo): array
{
    $payload = build_medicine_catalog_payload($pdo);
    $errors = validate_medicine_catalog($payload);

    if ($errors !== []) {
        return [
            'ok' => false,
            'errors' => $errors,
            'payload' => $payload,
        ];
    }

    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return [
            'ok' => false,
            'errors' => ['Failed to encode medicine catalog as JSON.'],
            'payload' => $payload,
        ];
    }

    file_put_contents(medicine_catalog_file(), $json);

    return [
        'ok' => true,
        'errors' => [],
        'payload' => $payload,
    ];
}

function read_medicine_catalog(): array
{
    $path = medicine_catalog_file();
    if (!file_exists($path)) {
        return [
            'ok' => false,
            'errors' => ['The medicine catalog JSON file does not exist yet.'],
            'payload' => null,
        ];
    }

    $raw = file_get_contents($path);
    $payload = json_decode($raw ?: '', true);

    if (!is_array($payload)) {
        return [
            'ok' => false,
            'errors' => ['The medicine catalog JSON file could not be decoded.'],
            'payload' => null,
        ];
    }

    $errors = validate_medicine_catalog($payload);

    return [
        'ok' => $errors === [],
        'errors' => $errors,
        'payload' => $payload,
    ];
}
