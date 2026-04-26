<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['token' => csrf_token()], JSON_UNESCAPED_SLASHES);
