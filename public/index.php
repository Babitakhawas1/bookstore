<?php
require_once "../config/config.php";

// Fetch all books
$stmt = $pdo->prepare("SELECT * FROM books ORDER BY id DESC");
$stmt->execute();
$books = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookstore - All Books</title>
    <style>
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
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .top-links a {
            margin-right: 15px;
        }
    </style>
</head>
<body>

<h1>📚 Bookstore</h1>

<div class="top-links">
    <a href="book_create.php">➕ Add New Book</a>
    <a href="login.php">Login</a>
</div>

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
