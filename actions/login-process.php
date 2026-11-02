<?php

session_start();

require_once '../config/database.php';

// 1. Получаем данные из формы
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// 2. Проверяем заполнение полей
if ($email === '' || $password === '') {
    exit('Fill in all fields');
}

// 3. Ищем пользователя в базе
$stmt = $pdo->prepare(
    'SELECT * FROM users WHERE email = ?'
);

$stmt->execute([$email]);

$user = $stmt->fetch();

// 4. Проверяем пользователя и пароль
if (!$user || !password_verify($password, $user['password_hash'])) {
    exit('Incorrect email or password');
}

// 5. Запоминаем пользователя в сессии
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['nickname'] = $user['nickname'];
$_SESSION['role'] = $user['role'];
$_SESSION['photo'] = $user['photo'] ?? null;

// 6. Переходим в профиль
header('Location: ../profile.php');
exit;