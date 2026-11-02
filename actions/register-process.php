<?php

session_start();

// Проверяем отправку формы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

// Проверяем CSRF-токен
if (
    !isset($_SESSION['csrf_token']) ||
    !isset($_POST['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['register_errors'] = [
        'Form expired. Please refresh the page and try again.'
    ];

    header('Location: ../register.php');
    exit;
}

// Получаем данные формы
$nickname = trim($_POST['nickname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

$errors = [];

// Проверяем никнейм
if ($nickname === '') {
    $errors[] = 'Enter your nickname.';
} elseif (preg_match('/^.{2,50}$/us', $nickname) !== 1) {
    $errors[] = 'Nickname must contain 2–50 characters.';
}

// Проверяем email
if (
    $email === '' ||
    strlen($email) > 255 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    $errors[] = 'Enter a valid email address.';
}

// Проверяем пароль
if ($password === '') {
    $errors[] = 'Enter your password.';
} elseif (strlen($password) < 8 || strlen($password) > 72) {
    $errors[] = 'Password must contain 8–72 bytes.';
}

// Проверяем подтверждение пароля
if ($password !== $password_confirm) {
    $errors[] = 'Passwords do not match.';
}

// Подключаем базу данных
require_once __DIR__ . '/../config/database.php';

// Проверяем, существует ли пользователь с таким email
if (empty($errors)) {

    $stmt = $pdo->prepare(
        'SELECT id FROM users WHERE email = ?'
    );

    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $errors[] = 'This email is already registered.';
    }
}

// Если есть ошибки, возвращаем пользователя к форме
if (!empty($errors)) {

    $_SESSION['register_errors'] = $errors;

    $_SESSION['register_old'] = [
        'nickname' => $nickname,
        'email' => $email
    ];

    header('Location: ../register.php');
    exit;
}

// Хешируем пароль
$password_hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

try {

    // Добавляем пользователя в базу данных
    $stmt = $pdo->prepare(
        'INSERT INTO users (nickname, email, password_hash)
         VALUES (?, ?, ?)'
    );

    $stmt->execute([
        $nickname,
        $email,
        $password_hash
    ]);

} catch (PDOException $e) {

    $_SESSION['register_errors'] = [
        'Could not create account. Please try again.'
    ];

    $_SESSION['register_old'] = [
        'nickname' => $nickname,
        'email' => $email
    ];

    header('Location: ../register.php');
    exit;
}

// Сообщение об успешной регистрации
$_SESSION['register_success'] = 'Registration successful! You can now log in.';

header('Location: ../register.php');
exit;