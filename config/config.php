<?php
// Start secure session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$host = "localhost";
$port = "3307";
$dbname = "bookstore_db";
$username = "root";
$password = "";

// Build DSN
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // Create PDO and store in $pdo (GLOBAL)
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // If connection fails, stop everything with clear error
    die("Database connection failed: " . $e->getMessage());
}

// Simple helper to escape output (XSS protection)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
