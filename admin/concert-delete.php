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

// Проверяем метод отправки
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

$id = filter_var(
    $_POST['concert_id'] ?? '',
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header('Location: index.php?delete=error');
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $pdo->beginTransaction();

    // Получаем и блокируем концерт
    $stmt = $pdo->prepare(
        'SELECT id FROM concerts WHERE id = ? FOR UPDATE'
    );

    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        $pdo->rollBack();

        header('Location: index.php?delete=error');
        exit;
    }

    // Проверяем, есть ли бронирования
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM bookings WHERE concert_id = ?'
    );

    $stmt->execute([$id]);

    $bookingsCount = (int) $stmt->fetchColumn();

    // Не удаляем концерт с существующими бронями
    if ($bookingsCount > 0) {
        $pdo->rollBack();

        header('Location: index.php?delete=has_bookings');
        exit;
    }

    // Удаляем концерт
    $stmt = $pdo->prepare(
        'DELETE FROM concerts WHERE id = ?'
    );

    $stmt->execute([$id]);

    $pdo->commit();

    header('Location: index.php?deleted=success');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header('Location: index.php?delete=error');
    exit;
}