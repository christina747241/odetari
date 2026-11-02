<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Разрешаем только отправку формы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../profile.php');
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

// Получаем никнейм
$nickname = trim($_POST['nickname'] ?? '');

// Проверяем длину никнейма
if (preg_match('/^.{2,50}$/us', $nickname) !== 1) {
    header('Location: ../profile-edit.php?error=nickname');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Получаем текущую фотографию пользователя
$stmt = $pdo->prepare(
    'SELECT photo FROM users WHERE id = ?'
);

$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    exit('User not found');
}

$oldPhoto = $user['photo'];
$photoPath = $oldPhoto;
$newFilePath = null;

// Проверяем, выбрал ли пользователь новую фотографию
if (
    isset($_FILES['photo']) &&
    $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $file = $_FILES['photo'];

    // Проверяем ошибки загрузки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if (
            $file['error'] === UPLOAD_ERR_INI_SIZE ||
            $file['error'] === UPLOAD_ERR_FORM_SIZE
        ) {
            header('Location: ../profile-edit.php?error=too_large');
        } else {
            header('Location: ../profile-edit.php?error=upload');
        }

        exit;
    }

    // Максимальный размер — 5 МБ
    if ($file['size'] > 5 * 1024 * 1024) {
        header('Location: ../profile-edit.php?error=too_large');
        exit;
    }

    // Проверяем настоящий тип файла
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowedTypes[$mime])) {
        header('Location: ../profile-edit.php?error=type');
        exit;
    }

    // Проверяем, что файл действительно является изображением
    $imageInfo = getimagesize($file['tmp_name']);

    if (
        $imageInfo === false ||
        $imageInfo['mime'] !== $mime ||
        $imageInfo[0] > 8000 ||
        $imageInfo[1] > 8000
    ) {
        header('Location: ../profile-edit.php?error=type');
        exit;
    }

    // Создаём папку, если её ещё нет
    $uploadDir = __DIR__ . '/../uploads/profiles/';

    if (
        !is_dir($uploadDir) &&
        !mkdir($uploadDir, 0755, true) &&
        !is_dir($uploadDir)
    ) {
        header('Location: ../profile-edit.php?error=upload');
        exit;
    }

    // Генерируем безопасное имя файла
    $filename = bin2hex(random_bytes(16))
        . '.'
        . $allowedTypes[$mime];

    $newFilePath = $uploadDir . $filename;

    // Перемещаем фотографию в папку uploads/profiles
    if (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
        header('Location: ../profile-edit.php?error=upload');
        exit;
    }

    $photoPath = 'uploads/profiles/' . $filename;
}

try {
    // Сохраняем никнейм и фотографию
    $stmt = $pdo->prepare(
        'UPDATE users
         SET nickname = ?, photo = ?
         WHERE id = ?'
    );

    $stmt->execute([
        $nickname,
        $photoPath,
        $_SESSION['user_id']
    ]);

} catch (Throwable $e) {
    // Удаляем новый файл, если запись в БД не удалась
    if ($newFilePath !== null && is_file($newFilePath)) {
        unlink($newFilePath);
    }

    header('Location: ../profile-edit.php?error=save');
    exit;
}

// Обновляем данные текущей сессии
$_SESSION['nickname'] = $nickname;
$_SESSION['photo'] = $photoPath;

// Удаляем старую загруженную фотографию, если она была
if (
    $oldPhoto &&
    $oldPhoto !== $photoPath &&
    preg_match(
        '#^uploads/profiles/[a-f0-9]{32}\.(jpg|png|webp)$#',
        $oldPhoto
    )
) {
    $oldFilePath = __DIR__ . '/../' . $oldPhoto;

    if (is_file($oldFilePath)) {
        unlink($oldFilePath);
    }
}

// Возвращаемся в профиль
header('Location: ../profile.php?updated=success');
exit;