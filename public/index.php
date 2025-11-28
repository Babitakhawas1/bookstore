<?php
require_once "../config/config.php";

$titleSearch = trim($_GET['title'] ?? '');
$genreSearch = trim($_GET['genre'] ?? '');
$yearSearch  = trim($_GET['year'] ?? '');

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if ($titleSearch !== '') {
    $sql .= " AND title LIKE :title";
    $params[':title'] = '%' . $titleSearch . '%';
}

if ($genreSearch !== '') {
    $sql .= " AND genre LIKE :genre";
    $params[':genre'] = '%' . $genreSearch . '%';
}

if ($yearSearch !== '' && ctype_digit($yearSearch)) {
    $sql .= " AND publication_year = :year";
    $params[':year'] = (int)$yearSearch;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

echo $twig->render('books_list.html.twig', [
    'books' => $books,
    'titleSearch' => $titleSearch,
    'genreSearch' => $genreSearch,
    'yearSearch' => $yearSearch,
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null
]);
