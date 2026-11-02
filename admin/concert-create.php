<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Проверяем роль
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied');
}

// Проверяем метод отправки формы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Проверяем CSRF-токен
if (
    !isset($_SESSION['csrf_token'], $_POST['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    exit('Invalid form token');
}

require_once __DIR__ . '/../config/database.php';

// Получаем данные формы
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$image = trim($_POST['image'] ?? '');
$eventDate = trim($_POST['event_date'] ?? '');
$location = trim($_POST['location'] ?? '');

$capacity = filter_var(
    $_POST['capacity'] ?? '',
    FILTER_VALIDATE_INT
);

// Проверяем обязательные поля
if (
    $title === '' ||
    $description === '' ||
    $image === '' ||
    $eventDate === '' ||
    $location === '' ||
    $capacity === false ||
    $capacity < 1
) {
    exit('Please fill in all fields correctly');
}

// Проверяем дату
$date = DateTime::createFromFormat(
    '!Y-m-d\TH:i',
    $eventDate
);

if (
    !$date ||
    $date->format('Y-m-d\TH:i') !== $eventDate
) {
    exit('Invalid date');
}

// Преобразуем дату для MySQL
$eventDate = $date->format('Y-m-d H:i:s');

// Добавляем концерт
$stmt = $pdo->prepare(
    'INSERT INTO concerts
        (title, description, image, event_date, location, capacity)
    VALUES (?, ?, ?, ?, ?, ?)'
);

$stmt->execute([
    $title,
    $description,
    $image,
    $eventDate,
    $location,
    $capacity
]);

// Возвращаемся в панель администратора
header('Location: index.php?created=success');
exit;