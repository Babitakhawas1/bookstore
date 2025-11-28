<?php
require_once "../config/config.php";

$errors = [];
$username = "";
$password = "";
$captcha_input = "";

$message = "";
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $message = "Registration successful. You can now log in.";
}

function generate_captcha() {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    return [$a, $b];
}

list($number1, $number2) = generate_captcha();

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

    $expected = $_SESSION['captcha_answer'] ?? null;
    if (!ctype_digit($captcha_input) || (int)$captcha_input !== (int)$expected) {
        $errors[] = "Incorrect CAPTCHA answer.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :u");
        $stmt->execute([':u' => $username]);
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
    }

    list($number1, $number2) = generate_captcha();
}

echo $twig->render("login.html.twig", [
    'username' => $username,
    'captcha_input' => $captcha_input,
    'errors' => $errors,
    'message' => $message,
    'number1' => $number1,
    'number2' => $number2,
    'user_id' => $_SESSION['user_id'] ?? null,
    'username_session' => $_SESSION['username'] ?? null
]);
