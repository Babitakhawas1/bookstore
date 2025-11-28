<?php
require_once "../config/config.php";

$errors = [];
$username = "";
$email = "";
$captcha_input = "";

function generate_captcha() {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    return [$a, $b];
}

list($number1, $number2) = generate_captcha();

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
        $errors[] = "Valid email required.";
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($confirm_password !== $password) {
        $errors[] = "Passwords do not match.";
    }

    $expected = $_SESSION['captcha_answer'] ?? null;
    if (!ctype_digit($captcha_input) || (int)$captcha_input !== (int)$expected) {
        $errors[] = "Incorrect CAPTCHA answer.";
    }

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e");
        $check->execute([':u' => $username, ':e' => $email]);

        if ($check->fetch()) {
            $errors[] = "Username or email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, role)
                VALUES (:u, :e, :p, 'user')
            ");

            $insert->execute([
                ':u' => $username,
                ':e' => $email,
                ':p' => $hash
            ]);

            header("Location: login.php?registered=1");
            exit;
        }
    }

    list($number1, $number2) = generate_captcha();
}

echo $twig->render("register.html.twig", [
    'errors' => $errors,
    'username' => $username,
    'email' => $email,
    'captcha_input' => $captcha_input,
    'number1' => $number1,
    'number2' => $number2,
    'user_id' => $_SESSION['user_id'] ?? null,
    'username_session' => $_SESSION['username'] ?? null
]);
