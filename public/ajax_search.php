<?php
require_once "../config/config.php";

header('Content-Type: application/json; charset=utf-8');

$term = trim($_GET['term'] ?? '');

if ($term === '') {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT title 
        FROM books 
        WHERE title LIKE :term 
        ORDER BY title ASC 
        LIMIT 5
    ");
    $stmt->execute([':term' => '%' . $term . '%']);
    $titles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($titles);
} catch (PDOException $e) {
    echo json_encode([]);
}
