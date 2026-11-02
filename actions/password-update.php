<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Разрешаем только отправку формы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../change-password.php');
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

// Получаем данные формы
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

// Проверяем заполнение полей
if (
    $currentPassword === '' ||
    $newPassword === '' ||
    $passwordConfirm === ''
) {
    header('Location: ../change-password.php?error=empty');
    exit;
}

// Проверяем длину нового пароля
if (strlen($newPassword) < 8 || strlen($newPassword) > 72) {
    header('Location: ../change-password.php?error=length');
    exit;
}

// Проверяем подтверждение
if ($newPassword !== $passwordConfirm) {
    header('Location: ../change-password.php?error=confirm');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Получаем хеш текущего пароля
$stmt = $pdo->prepare(
    'SELECT password_hash FROM users WHERE id = ?'
);

$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    exit('User not found');
}

// Проверяем текущий пароль
if (!password_verify($currentPassword, $user['password_hash'])) {
    header('Location: ../change-password.php?error=current');
    exit;
}

// Проверяем, отличается ли новый пароль от старого
if (password_verify($newPassword, $user['password_hash'])) {
    header('Location: ../change-password.php?error=same');
    exit;
}

// Создаём хеш нового пароля
$newPasswordHash = password_hash(
    $newPassword,
    PASSWORD_DEFAULT
);

try {
    // Сохраняем новый хеш в базе данных
    $stmt = $pdo->prepare(
        'UPDATE users SET password_hash = ? WHERE id = ?'
    );

    $stmt->execute([
        $newPasswordHash,
        $_SESSION['user_id']
    ]);

} catch (Throwable $e) {
    header('Location: ../change-password.php?error=save');
    exit;
}

// Обновляем идентификатор сессии
session_regenerate_id(true);

// Возвращаемся на страницу смены пароля
header('Location: ../change-password.php?updated=success');
exit;