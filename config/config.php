<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect environment: local (XAMPP) vs mi-linux
$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost');

// Database settings
if ($isLocal) {
    // LOCAL XAMPP SETTINGS
    $dbHost = "localhost";
    $dbPort = "3307";
    $dbName = "bookstore_db";
    $dbUser = "root";
    $dbPass = "";
} else {
    // MI-LINUX SETTINGS
    $dbHost = "localhost";
    $dbPort = "3306";
    $dbName = "db2420693";
    $dbUser = "2420693";
    $dbPass = "Babitakhawas@1866";
}

// DSN
$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";

try {
    $pdo = new PDO(
        $dsn,
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Simple escaping helper (still useful even with Twig)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Twig setup
require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,   // keep off for development
    'debug' => true
]);
