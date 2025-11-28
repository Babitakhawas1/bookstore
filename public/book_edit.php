<?php
require_once "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$book = null;

$id = $_GET['id'] ?? null;

if ($id === null || !ctype_digit($id)) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book = [
        'id' => $id,
        'title' => trim($_POST['title'] ?? ''),
        'author' => trim($_POST['author'] ?? ''),
        'genre' => trim($_POST['genre'] ?? ''),
        'publication_year' => trim($_POST['publication_year'] ?? ''),
        'price' => trim($_POST['price'] ?? '')
    ];

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
                UPDATE books
                SET title = :title,
                    author = :author,
                    genre = :genre,
                    publication_year = :publication_year,
                    price = :price
                WHERE id = :id
            ");

            $stmt->execute([
                ':title' => $book['title'],
                ':author' => $book['author'],
                ':genre' => $book['genre'],
                ':publication_year' => (int)$book['publication_year'],
                ':price' => $book['price'] === '' ? null : $book['price'],
                ':id' => (int)$id
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $errors[] = "Database error: " . e($e->getMessage());
        }
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute([':id' => (int)$id]);
    $book = $stmt->fetch();

    if (!$book) {
        echo "Book not found.";
        exit;
    }
}

echo $twig->render('book_form.html.twig', [
    'page_title' => 'Edit Book',
    'form_title' => 'Edit Book',
    'submit_label' => 'Update Book',
    'book' => $book,
    'errors' => $errors,
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null
]);
