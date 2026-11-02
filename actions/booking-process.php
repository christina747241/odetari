<?php
session_start();

require_once '../config/database.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Получаем данные формы
$concertId = filter_var(
    $_POST['concert_id'] ?? '',
    FILTER_VALIDATE_INT
);

$quantity = filter_var(
    $_POST['quantity'] ?? '',
    FILTER_VALIDATE_INT
);

// Проверяем ID концерта
if (!$concertId || $concertId < 1) {
    header('Location: ../index.php');
    exit;
}

// Проверяем CSRF-токен
if (
    !isset($_SESSION['csrf_token']) ||
    !isset($_POST['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header("Location: ../concert.php?id=$concertId&booking=error");
    exit;
}

// Проверяем количество билетов
if ($quantity === false || $quantity < 1 || $quantity > 10) {
    header("Location: ../concert.php?id=$concertId&booking=error");
    exit;
}

try {
    // Начинаем транзакцию
    $pdo->beginTransaction();

    // Получаем вместимость концерта и блокируем его запись
    $stmt = $pdo->prepare(
        'SELECT capacity FROM concerts WHERE id = ? FOR UPDATE'
    );

    $stmt->execute([$concertId]);
    $concert = $stmt->fetch();

    if (!$concert) {
        $pdo->rollBack();

        header("Location: ../index.php");
        exit;
    }

    // Считаем количество уже забронированных билетов
    $stmt = $pdo->prepare(
        'SELECT COALESCE(SUM(quantity), 0) AS booked
         FROM bookings
         WHERE concert_id = ?'
    );

    $stmt->execute([$concertId]);
    $booked = (int) $stmt->fetch()['booked'];

    // Проверяем свободные места
    if ($booked + $quantity > (int) $concert['capacity']) {
        $pdo->rollBack();

        header("Location: ../concert.php?id=$concertId&booking=full");
        exit;
    }

    // Сохраняем бронь
    $stmt = $pdo->prepare(
        'INSERT INTO bookings (user_id, concert_id, quantity)
         VALUES (?, ?, ?)'
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $concertId,
        $quantity
    ]);

    // Подтверждаем изменения
    $pdo->commit();

    header("Location: ../concert.php?id=$concertId&booking=success");
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: ../concert.php?id=$concertId&booking=error");
    exit;
}