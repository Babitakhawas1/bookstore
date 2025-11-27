<?php
require_once "../config/config.php";

// Get ID safely
$id = $_GET['id'] ?? null;

// Validate ID
if ($id === null || !ctype_digit($id)) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
    $stmt->execute([':id' => (int)$id]);
} catch (PDOException $e) {
    die("Error deleting book: " . e($e->getMessage()));
}

header("Location: index.php");
exit;
