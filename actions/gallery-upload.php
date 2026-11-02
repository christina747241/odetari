<?php
session_start();

// Только авторизованные пользователи
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Только отправка формы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php#gallery');
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

// Проверяем, загружен ли файл
if (!isset($_FILES['photo'])) {
    header('Location: ../index.php?upload=error#gallery');
    exit;
}

$file = $_FILES['photo'];

// Проверяем ошибки загрузки
if ($file['error'] !== UPLOAD_ERR_OK) {
    if (
        $file['error'] === UPLOAD_ERR_INI_SIZE ||
        $file['error'] === UPLOAD_ERR_FORM_SIZE
    ) {
        header('Location: ../index.php?upload=too_large#gallery');
    } else {
        header('Location: ../index.php?upload=error#gallery');
    }

    exit;
}

// Максимум 5 МБ
if ($file['size'] > 5 * 1024 * 1024) {
    header('Location: ../index.php?upload=too_large#gallery');
    exit;
}

// Разрешённые форматы и расширения
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

// Определяем тип содержимого файла
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

if (!isset($allowedTypes[$mime])) {
    header('Location: ../index.php?upload=type#gallery');
    exit;
}

// Дополнительно проверяем, что файл действительно является изображением
$imageInfo = getimagesize($file['tmp_name']);

if (
    $imageInfo === false ||
    $imageInfo['mime'] !== $mime ||
    $imageInfo[0] > 8000 ||
    $imageInfo[1] > 8000
) {
    header('Location: ../index.php?upload=type#gallery');
    exit;
}

// Проверяем подпись
$caption = trim($_POST['caption'] ?? '');

if (strlen($caption) > 600) {
    header('Location: ../index.php?upload=error#gallery');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Формируем безопасное имя файла
$filename = bin2hex(random_bytes(16))
    . '.'
    . $allowedTypes[$mime];

$uploadDir = __DIR__ . '/../uploads/gallery/';
$destination = $uploadDir . $filename;
$imagePath = 'uploads/gallery/' . $filename;

// Проверяем существование папки и перемещаем файл
if (
    !is_dir($uploadDir) ||
    !is_uploaded_file($file['tmp_name']) ||
    !move_uploaded_file($file['tmp_name'], $destination)
) {
    header('Location: ../index.php?upload=error#gallery');
    exit;
}

try {
    // Сохраняем информацию о фотографии
    $stmt = $pdo->prepare(
        'INSERT INTO gallery_photos
            (user_id, image_path, caption)
        VALUES (?, ?, ?)'
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $imagePath,
        $caption === '' ? null : $caption
    ]);

} catch (Throwable $e) {
    // Если запись в БД не сохранилась, удаляем файл
    if (is_file($destination)) {
        unlink($destination);
    }

    header('Location: ../index.php?upload=error#gallery');
    exit;
}

// Возвращаемся к галерее
header('Location: ../index.php?upload=success#gallery');
exit;