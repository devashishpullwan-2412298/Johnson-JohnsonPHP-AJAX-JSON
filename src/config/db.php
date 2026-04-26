<?php
// src/config/db.php

function getPDO(): PDO {
    $host = 'localhost';
    $db   = 'online_pharmacy';
    $user = 'root';
    $pass = '';

    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    try {
        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        error_log('DB Connection failed: ' . $e->getMessage());
        http_response_code(500);
        exit('Unable to connect to the database right now. Please try again later.');
    }
}
