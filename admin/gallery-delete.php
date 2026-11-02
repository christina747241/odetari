<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Проверяем права администратора
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied');
}

// Разрешаем только POST-запрос
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

// Получаем ID фотографии
$id = filter_var(
    $_POST['photo_id'] ?? '',
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header('Location: index.php?photo=error');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Получаем путь к фотографии
$stmt = $pdo->prepare(
    'SELECT image_path FROM gallery_photos WHERE id = ?'
);

$stmt->execute([$id]);
$photo = $stmt->fetch();

if (!$photo) {
    header('Location: index.php?photo=error');
    exit;
}

$imagePath = $photo['image_path'];

// Разрешаем удаление только файлов,
 // которые созданы нашим обработчиком загрузки
if (!preg_match(
    '#^uploads/gallery/[a-f0-9]{32}\.(jpg|png|webp)$#',
    $imagePath
)) {
    header('Location: index.php?photo=error');
    exit;
}

// Формируем путь к файлу на сервере
$filePath = __DIR__ . '/../' . $imagePath;

try {
    // Удаляем запись из базы данных
    $stmt = $pdo->prepare(
        'DELETE FROM gallery_photos WHERE id = ?'
    );

    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        header('Location: index.php?photo=error');
        exit;
    }

} catch (Throwable $e) {
    header('Location: index.php?photo=error');
    exit;
}

// Удаляем сам файл
if (is_file($filePath) && !unlink($filePath)) {
    header('Location: index.php?photo=file_error');
    exit;
}

// Возвращаемся в админ-панель
header('Location: index.php?photo=deleted');
exit;