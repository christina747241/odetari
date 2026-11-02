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

// Создаём CSRF-токен, если его нет
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../config/database.php';

// Получаем ID концерта
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

// Получаем данные концерта
$stmt = $pdo->prepare(
    'SELECT * FROM concerts WHERE id = ?'
);

$stmt->execute([$id]);
$concert = $stmt->fetch();

if (!$concert) {
    exit('Concert not found');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit concert</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/edit.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

    <main class="edit-page">

        <div class="edit">

            <!-- Заголовок -->
            <div class="edit__heading">

                <p class="edit__subtitle">
                    ODECORE / EVENT MANAGEMENT
                </p>

                <h1>Edit concert</h1>

                <p class="edit__description">
                    Update event information and manage your concert.
                </p>

            </div>

            <!-- Сообщения об ошибках -->
            <?php if (($_GET['error'] ?? '') === 'capacity'): ?>

                <p class="edit__error">
                    Capacity cannot be less than the number of booked tickets.
                </p>

            <?php elseif (($_GET['error'] ?? '') === 'invalid'): ?>

                <p class="edit__error">
                    Please fill in all fields correctly.
                </p>

            <?php elseif (($_GET['error'] ?? '') === 'save'): ?>

                <p class="edit__error">
                    Could not save the changes.
                </p>

            <?php endif; ?>


            <!-- Форма редактирования -->
            <form
                action="concert-update.php"
                method="POST"
                class="edit__form"
            >

                <input
                    type="hidden"
                    name="concert_id"
                    value="<?= (int) $concert['id'] ?>"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        $_SESSION['csrf_token'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <!-- Название -->
                <div class="edit__field">

                    <label for="title">Concert title</label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?= htmlspecialchars(
                            $concert['title'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>

                <!-- Описание -->
                <div class="edit__field">

                    <label for="description">Description</label>

                    <textarea
                        id="description"
                        name="description"
                        required
                    ><?= htmlspecialchars(
                        $concert['description'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>

                <!-- Изображение -->
                <div class="edit__field">

                    <label for="image">Concert image</label>

                    <input
                        type="text"
                        id="image"
                        name="image"
                        value="<?= htmlspecialchars(
                            $concert['image'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>

                <!-- Дата -->
                <div class="edit__field">

                    <label for="event_date">Date and time</label>

                    <input
                        type="datetime-local"
                        id="event_date"
                        name="event_date"
                        value="<?= date(
                            'Y-m-d\TH:i',
                            strtotime($concert['event_date'])
                        ) ?>"
                        required
                    >

                </div>

                <!-- Место -->
                <div class="edit__field">

                    <label for="location">Location</label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="<?= htmlspecialchars(
                            $concert['location'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>

                <!-- Вместимость -->
                <div class="edit__field">

                    <label for="capacity">Capacity</label>

                    <input
                        type="number"
                        id="capacity"
                        name="capacity"
                        min="1"
                        value="<?= (int) $concert['capacity'] ?>"
                        required
                    >

                </div>

                <!-- Кнопки -->
                <div class="edit__actions">

                    <button
                        type="submit"
                        class="edit__save"
                    >
                        SAVE CHANGES
                    </button>

                    <a
                        href="index.php"
                        class="edit__back"
                    >
                        Back to admin panel
                    </a>

                </div>

            </form>

        </div>

    </main>

</body>
</html>