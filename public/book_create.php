<?php
require_once "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$book = [
    'title' => '',
    'author' => '',
    'genre' => '',
    'publication_year' => '',
    'price' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book['title'] = trim($_POST['title'] ?? '');
    $book['author'] = trim($_POST['author'] ?? '');
    $book['genre'] = trim($_POST['genre'] ?? '');
    $book['publication_year'] = trim($_POST['publication_year'] ?? '');
    $book['price'] = trim($_POST['price'] ?? '');

    if ($book['title'] === '') {
        $errors[] = "Title is required.";
    }
    if ($book['author'] === '') {
        $errors[] = "Author is required.";
    }
    if ($book['genre'] === '') {
        $errors[] = "Genre is required.";
    }
    if ($book['publication_year'] === '' || !ctype_digit($book['publication_year'])) {
        $errors[] = "Publication year must be a valid number.";
    }
    if ($book['price'] !== '' && !is_numeric($book['price'])) {
        $errors[] = "Price must be a valid number (or leave blank).";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO books (title, author, genre, publication_year, price)
                VALUES (:title, :author, :genre, :publication_year, :price)
            ");

            $stmt->execute([
                ':title' => $book['title'],
                ':author' => $book['author'],
                ':genre' => $book['genre'],
                ':publication_year' => (int)$book['publication_year'],
                ':price' => $book['price'] === '' ? null : $book['price']
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . e($e->getMessage());
        }
    }
}

echo $twig->render('book_form.html.twig', [
    'page_title' => 'Add New Book',
    'form_title' => 'Add New Book',
    'submit_label' => 'Save Book',
    'book' => $book,
    'errors' => $errors,
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null
]);
