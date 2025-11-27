<?php
require_once "../config/config.php";

$errors = [];
$username = "";
$password = "";
$captcha_input = "";

function generate_captcha() {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    return [$a, $b];
}

$number1 = null;
$number2 = null;

$registeredMessage = "";
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $registeredMessage = "Registration successful. You can now log in.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha_input = trim($_POST['captcha'] ?? '');

    if ($username === '') {
        $errors[] = "Username is required.";
    }
    if ($password === '') {
        $errors[] = "Password is required.";
    }

    $expectedCaptcha = $_SESSION['captcha_answer'] ?? null;
    if ($expectedCaptcha === null || !ctype_digit($captcha_input) || (int)$captcha_input !== (int)$expectedCaptcha) {
        $errors[] = "Incorrect CAPTCHA answer.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . e($e->getMessage());
        }
    }

    list($number1, $number2) = generate_captcha();
} else {
    list($number1, $number2) = generate_captcha();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Bookstore</title>
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
        input[type="password"],
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
        .message {
            background: #e0ffe0;
            border: 1px solid #90ff90;
            padding: 10px;
            margin-bottom: 15px;
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

<h1>Login</h1>

<?php if ($registeredMessage): ?>
    <div class="message">
        <?= e($registeredMessage); ?>
    </div>
<?php endif; ?>

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
    <label for="username">Username *</label>
    <input type="text" name="username" id="username" required
           value="<?= e($username); ?>">

    <label for="password">Password *</label>
    <input type="password" name="password" id="password" required>

    <label>Solve to verify you are human:</label>
    <p><strong><?= e($number1); ?> + <?= e($number2); ?> = ?</strong></p>
    <input type="number" name="captcha" required
           value="<?= e($captcha_input); ?>">

    <button type="submit">Login</button>
</form>

<p>Don’t have an account? <a href="register.php">Register here</a>.</p>

</body>
</html>
