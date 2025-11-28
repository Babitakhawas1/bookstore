<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$host = "localhost";
$port = "3307";
$dbname = "bookstore_db";
$username = "root";
$password = "";

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
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
    die("Database connection failed: " . $e->getMessage());
}

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Twig setup
require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);
