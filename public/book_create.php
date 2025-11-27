<?php
require_once "../config/config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$title = "";
$author = "";
$genre = "";
$publication_year = "";
$price = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input filtering
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $publication_year = trim($_POST['publication_year'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // Validation
    if ($title === '') {
        $errors[] = "Title is required.";
    }
    if ($author === '') {
        $errors[] = "Author is required.";
    }
    if ($genre === '') {
        $errors[] = "Genre is required.";
    }
    if ($publication_year === '' || !ctype_digit($publication_year)) {
        $errors[] = "Publication year must be a valid number.";
    }
    if ($price !== '' && !is_numeric($price)) {
        $errors[] = "Price must be a valid number (or leave blank).";
    }

    // If no errors, insert into DB (CREATE)
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO books (title, author, genre, publication_year, price)
                VALUES (:title, :author, :genre, :publication_year, :price)
            ");

            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':genre' => $genre,
                ':publication_year' => (int)$publication_year,
                ':price' => $price === '' ? null : $price
            ]);

            // Redirect back to list
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . e($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        form {
            max-width: 400px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .errors {
            background: #ffe0e0;
            border: 1px solid #ff9090;
            padding: 10px;
            margin-bottom: 15px;
        }
        .errors li {
            margin-left: 20px;
        }
        .back-link {
            margin-bottom: 15px;
            display: inline-block;
        }
        button {
            margin-top: 15px;
            padding: 8px 15px;
        }
    </style>
</head>
<body>

<a class="back-link" href="index.php">&larr; Back to Book List</a>

<h1>Add New Book</h1>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <strong>Please fix the following errors:</strong>
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="">
    <label for="title">Title *</label>
    <input type="text" name="title" id="title" required
           value="<?= e($title); ?>">

    <label for="author">Author *</label>
    <input type="text" name="author" id="author" required
           value="<?= e($author); ?>">

    <label for="genre">Genre *</label>
    <input type="text" name="genre" id="genre" required
           value="<?= e($genre); ?>">

    <label for="publication_year">Publication Year *</label>
    <input type="number" name="publication_year" id="publication_year" required
           value="<?= e($publication_year); ?>">

    <label for="price">Price (optional)</label>
    <input type="text" name="price" id="price"
           value="<?= e($price); ?>">

    <button type="submit">Save Book</button>
</form>

</body>
</html>
