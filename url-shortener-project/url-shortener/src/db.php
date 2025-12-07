<?php

/**
 * Create (once) and return a shared PDO database connection.
 * Uses configuration values from config.php.
 *
 * @return PDO
 */
function db()
{
    static $pdo = null;

    // Return existing PDO instance if already created
    if ($pdo !== null) {
        return $pdo;
    }

    // Load database configuration
    $config = require __DIR__ . '/config.php';

    $host = $config['DB_HOST'];
    $name = $config['DB_DATABASE'];
    $user = $config['DB_USER'];
    $pass = $config['DB_PASS'];

    $dsn  = "mysql:host={$host};dbname={$name};charset=utf8mb4";

    try {
        // Create new PDO connection
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

    } catch (PDOException $e) {
        // Stop execution on connection failure
        die("Database connection failed: " . $e->getMessage());
    }

    return $pdo;
}
