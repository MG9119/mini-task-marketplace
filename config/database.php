<?php

$config = require __DIR__ . "/database.local.php";

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
        $config["username"],
        $config["password"],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);

    exit;
}