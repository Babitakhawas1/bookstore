<?php
require_once "../config/config.php";

// Read search inputs
$titleSearch = trim($_GET['title'] ?? '');
$genreSearch = trim($_GET['genre'] ?? '');
$yearSearch  = trim($_GET['year'] ?? '');

// Build base query
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

// Add filters if provided (multi-criteria)
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookstore - All Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
        .top-links a {
            margin-right: 15px;
        }
        .search-form {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .search-form input {
            margin-right: 10px;
            padding: 5px;
        }
        .search-form button {
            padding: 5px 10px;
        }
    </style>
</head>
<body>

<h1>📚 Bookstore</h1>

<div class="top-links">
    <a href="book_create.php">➕ Add New Book</a>
    <a href="login.php">Login</a>
</div>

<h2>Search Books</h2>

<form method="get" action="" class="search-form">
    <input type="text" name="title" placeholder="Title contains..."
           value="<?= e($titleSearch); ?>">

    <input type="text" name="genre" placeholder="Genre..."
           value="<?= e($genreSearch); ?>">

    <input type="number" name="year" placeholder="Year"
           value="<?= e($yearSearch); ?>">

    <button type="submit">Search</button>
    <a href="index.php">Reset</a>
</form>

<h2>All Books</h2>

<?php if (count($books) === 0): ?>
    <p>No books found.</p>
<?php else: ?>
<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Author</th>
        <th>Genre</th>
        <th>Year</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= e($b['id']); ?></td>
            <td><?= e($b['title']); ?></td>
            <td><?= e($b['author']); ?></td>
            <td><?= e($b['genre']); ?></td>
            <td><?= e($b['publication_year']); ?></td>
            <td>$<?= e($b['price']); ?></td>
            <td>
                <a href="book_edit.php?id=<?= $b['id']; ?>">Edit</a> |
                <a href="book_delete.php?id=<?= $b['id']; ?>"
                   onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>
<?php endif; ?>

</body>
</html>
