<?php
session_start();

require_once '../config/database.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Проверяем CSRF-токен
if (
    !isset($_SESSION['csrf_token'], $_POST['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header('Location: ../profile.php?booking_cancelled=error');
    exit;
}

// Получаем ID бронирования
$bookingId = filter_var(
    $_POST['booking_id'] ?? '',
    FILTER_VALIDATE_INT
);

if (!$bookingId || $bookingId < 1) {
    header('Location: ../profile.php?booking_cancelled=error');
    exit;
}

// Удаляем только бронь текущего пользователя
$stmt = $pdo->prepare(
    'DELETE FROM bookings
    WHERE id = ? AND user_id = ?'
);

$stmt->execute([
    $bookingId,
    $_SESSION['user_id']
]);

// Проверяем, была ли удалена запись
if ($stmt->rowCount() > 0) {
    header('Location: ../profile.php?booking_cancelled=success');
} else {
    header('Location: ../profile.php?booking_cancelled=error');
}

exit;