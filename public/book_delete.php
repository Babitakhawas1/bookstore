<?php
require_once "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id === null || !ctype_digit($id)) {
    header("Location: index.php");
    exit;
}

// If POST → delete the book and redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: index.php");
    exit;
}

// Otherwise show confirmation page
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute([':id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    echo $twig->render("error.html.twig", [
        'message' => "Book not found.",
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null
    ]);
    exit;
}

echo $twig->render("book_delete_confirm.html.twig", [
    'book' => $book,
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null
]);
