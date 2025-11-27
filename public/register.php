<?php
require_once "../config/config.php";

$errors = [];
$username = "";
$email = "";
$password = "";
$confirm_password = "";
$captcha_input = "";

function generate_captcha() {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    return [$a, $b];
}

$number1 = null;
$number2 = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $captcha_input = trim($_POST['captcha'] ?? '');

    if ($username === '') {
        $errors[] = "Username is required.";
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($confirm_password === '' || $confirm_password !== $password) {
        $errors[] = "Password confirmation does not match.";
    }

    $expectedCaptcha = $_SESSION['captcha_answer'] ?? null;
    if ($expectedCaptcha === null || !ctype_digit($captcha_input) || (int)$captcha_input !== (int)$expectedCaptcha) {
        $errors[] = "Incorrect CAPTCHA answer.";
    }

    if (empty($errors)) {
        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $check->execute([
                ':username' => $username,
                ':email' => $email
            ]);

            if ($check->fetch()) {
                $errors[] = "Username or email already exists.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $insert = $pdo->prepare("
                    INSERT INTO users (username, email, password_hash, role)
                    VALUES (:username, :email, :password_hash, 'user')
                ");

                $insert->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password_hash' => $passwordHash
                ]);

                header("Location: login.php?registered=1");
                exit;
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
    <title>Register - Bookstore</title>
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
        input[type="email"],
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

<h1>Create an Account</h1>

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

    <label for="email">Email *</label>
    <input type="email" name="email" id="email" required
           value="<?= e($email); ?>">

    <label for="password">Password *</label>
    <input type="password" name="password" id="password" required>

    <label for="confirm_password">Confirm Password *</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <label>Solve to verify you are human:</label>
    <p><strong><?= e($number1); ?> + <?= e($number2); ?> = ?</strong></p>
    <input type="number" name="captcha" required
           value="<?= e($captcha_input); ?>">

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a>.</p>

</body>
</html>
