<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Проверяем права
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied');
}

// Разрешаем только отправку формы
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

// Получаем данные
$id = filter_var(
    $_POST['concert_id'] ?? '',
    FILTER_VALIDATE_INT
);

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$image = trim($_POST['image'] ?? '');
$eventDate = trim($_POST['event_date'] ?? '');
$location = trim($_POST['location'] ?? '');

$capacity = filter_var(
    $_POST['capacity'] ?? '',
    FILTER_VALIDATE_INT
);

// Проверяем поля
if (
    !$id || $id < 1 ||
    $title === '' ||
    $description === '' ||
    $image === '' ||
    $eventDate === '' ||
    $location === '' ||
    $capacity === false ||
    $capacity < 1
) {
    if ($id && $id > 0) {
        header("Location: concert-edit.php?id=$id&error=invalid");
    } else {
        header('Location: index.php');
    }

    exit;
}

// Проверяем формат даты
$date = DateTime::createFromFormat(
    '!Y-m-d\TH:i',
    $eventDate
);

if (!$date || $date->format('Y-m-d\TH:i') !== $eventDate) {
    header("Location: concert-edit.php?id=$id&error=invalid");
    exit;
}

$eventDate = $date->format('Y-m-d H:i:s');

try {
    $pdo->beginTransaction();

    // Проверяем наличие концерта и блокируем его запись
    $stmt = $pdo->prepare(
        'SELECT id FROM concerts WHERE id = ? FOR UPDATE'
    );

    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        $pdo->rollBack();
        header('Location: index.php');
        exit;
    }

    // Считаем забронированные билеты
    $stmt = $pdo->prepare(
        'SELECT COALESCE(SUM(quantity), 0) AS booked
        FROM bookings
        WHERE concert_id = ?'
    );

    $stmt->execute([$id]);
    $booked = (int) $stmt->fetch()['booked'];

    // Нельзя уменьшить вместимость ниже количества броней
    if ($capacity < $booked) {
        $pdo->rollBack();

        header("Location: concert-edit.php?id=$id&error=capacity");
        exit;
    }

    // Обновляем данные концерта
    $stmt = $pdo->prepare(
        'UPDATE concerts
        SET title = ?,
            description = ?,
            image = ?,
            event_date = ?,
            location = ?,
            capacity = ?
        WHERE id = ?'
    );

    $stmt->execute([
        $title,
        $description,
        $image,
        $eventDate,
        $location,
        $capacity,
        $id
    ]);

    $pdo->commit();

    header('Location: index.php?updated=success');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: concert-edit.php?id=$id&error=save");
    exit;
}