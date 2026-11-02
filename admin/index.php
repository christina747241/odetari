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

// Создаём CSRF-токен
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Подключаем базу данных
require_once __DIR__ . '/../config/database.php';

// Получаем все концерты
$stmt = $pdo->query(
    'SELECT * FROM concerts ORDER BY event_date ASC'
);

$concerts = $stmt->fetchAll();

// Получаем фотографии галереи
$stmt = $pdo->query(
    'SELECT gallery_photos.id,
            gallery_photos.image_path,
            gallery_photos.caption,
            gallery_photos.created_at,
            users.nickname
    FROM gallery_photos
    JOIN users ON gallery_photos.user_id = users.id
    ORDER BY gallery_photos.created_at DESC'
);

$galleryPhotos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/edit.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

    <main class="admin-page">

        <!-- Заголовок панели -->
        <header class="admin-header">

            <div>
                <p class="admin-header__subtitle">
                    ODECORE / MANAGEMENT SYSTEM
                </p>

                <h1>Admin Panel</h1>

                <p class="admin-header__description">
                    Manage concerts, events and community photos.
                </p>
            </div>

            <a href="../index.php" class="admin-header__back">
                VIEW WEBSITE →
            </a>

        </header>


        <section class="edit-page">

    <div class="edit">

        <!-- Заголовок -->
        <div class="edit__heading">

            <p class="edit__subtitle">
                ODECORE / EVENT MANAGEMENT
            </p>

            <h1>Add a concert</h1>

            <p class="edit__description">
                Create a new event and share it with your community.
            </p>

        </div>

        <!-- Сообщение об успешном добавлении -->
        <?php if (($_GET['created'] ?? '') === 'success'): ?>

            <p class="edit__success">
                Concert successfully added!
            </p>

        <?php endif; ?>

        <!-- Форма -->
        <form
            action="concert-create.php"
            method="POST"
            class="edit__form"
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
                    placeholder="Enter concert title"
                    required
                >

            </div>

            <!-- Описание -->
            <div class="edit__field">

                <label for="description">Description</label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe the concert..."
                    required
                ></textarea>

            </div>

            <!-- Изображение -->
            <div class="edit__field">

                <label for="image">Concert image</label>

                <input
                    type="text"
                    id="image"
                    name="image"
                    placeholder="img/concerts/concert4.jpg"
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
                    placeholder="City, Country"
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
                    value="100"
                    required
                >

            </div>

            <!-- Кнопки -->
            <div class="edit__actions">

                <button type="submit" class="edit__save">
                    ADD CONCERT +
                </button>

                <a href="../index.php" class="edit__back">
                    Back to website
                </a>

            </div>

        </form>

    </div>

</section>


        <!-- Существующие концерты -->
        <section class="admin-section">

            <div class="admin-section__heading">
                <div>
                    <p class="admin-section__label">02 / MANAGEMENT</p>
                    <h2>Existing concerts</h2>
                </div>

                <span class="admin-section__count">
                    <?= count($concerts) ?> EVENTS
                </span>
            </div>

            <?php if (($_GET['updated'] ?? '') === 'success'): ?>
                <p class="admin-notice admin-notice--success">
                    Concert successfully updated!
                </p>
            <?php endif; ?>

            <?php if (($_GET['deleted'] ?? '') === 'success'): ?>
                <p class="admin-notice admin-notice--success">
                    Concert successfully deleted!
                </p>
            <?php endif; ?>

            <?php if (($_GET['delete'] ?? '') === 'has_bookings'): ?>
                <p class="admin-notice admin-notice--error">
                    Cannot delete this concert because it has bookings.
                </p>
            <?php endif; ?>

            <?php if (($_GET['delete'] ?? '') === 'error'): ?>
                <p class="admin-notice admin-notice--error">
                    Could not delete the concert.
                </p>
            <?php endif; ?>

            <?php if (empty($concerts)): ?>

                <p class="admin-empty">No upcoming concerts yet.</p>

            <?php else: ?>

                <div class="admin-concerts-grid">

                    <?php foreach ($concerts as $index => $concert): ?>

                        <article class="concert-card">

                            <!-- Фото -->
                            <div class="concert-card__image">

                                <img
                                    src="../<?= htmlspecialchars(
                                        $concert['image'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $concert['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <span>
                                    <?= str_pad(
                                        (string) ($index + 1),
                                        2,
                                        '0',
                                        STR_PAD_LEFT
                                    ) ?>
                                </span>

                            </div>

                            <!-- Информация -->
                            <div class="concert-card__content">

                                <p class="concert-card__label">
                                    UPCOMING EVENT
                                </p>

                                <h4>
                                    <?= htmlspecialchars(
                                        $concert['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h4>

                                <p class="concert-card__description">
                                    <?= htmlspecialchars(
                                        $concert['description'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <p class="concert-card__description">
                                    <?= htmlspecialchars(
                                        $concert['location'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <p class="concert-card__description">
                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime($concert['event_date'])
                                    ) ?>
                                </p>

                                <p class="concert-card__description">
                                    Capacity: <?= (int) $concert['capacity'] ?>
                                </p>

                                <!-- Кнопки управления -->
                                <div class="admin-concert__actions">

                                    <a
                                        href="concert-edit.php?id=<?= (int) $concert['id'] ?>"
                                        class="admin-concert__edit"
                                    >
                                        EDIT
                                    </a>

                                    <form
                                        action="concert-delete.php"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this concert?')"
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

                                        <button
                                            type="submit"
                                            class="admin-concert__delete"
                                        >
                                            DELETE
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <!-- Фотографии галереи -->
        <section class="admin-section">

            <div class="admin-section__heading">
                <div>
                    <p class="admin-section__label">03 / COMMUNITY</p>
                    <h2>Gallery photos</h2>
                </div>

                <span class="admin-section__count">
                    <?= count($galleryPhotos) ?> PHOTOS
                </span>
            </div>


            <?php if (($_GET['photo'] ?? '') === 'deleted'): ?>

                <p class="admin-notice admin-notice--success">
                    Photo successfully deleted!
                </p>

            <?php elseif (($_GET['photo'] ?? '') === 'file_error'): ?>

                <p class="admin-notice admin-notice--error">
                    Photo was removed from the gallery, but its file
                    could not be deleted from the server.
                </p>

            <?php elseif (($_GET['photo'] ?? '') === 'error'): ?>

                <p class="admin-notice admin-notice--error">
                    Could not delete the photo.
                </p>

            <?php endif; ?>


            <?php if (empty($galleryPhotos)): ?>

                <p class="admin-empty">No uploaded photos yet.</p>

            <?php else: ?>

                <div class="admin-gallery">

                    <?php foreach ($galleryPhotos as $photo): ?>

                        <article class="admin-photo">

                            <div class="admin-photo__image">

                                <img
                                    src="../<?= htmlspecialchars(
                                        $photo['image_path'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="Gallery photo"
                                >

                            </div>

                            <div class="admin-photo__content">

                                <h3>
                                    <?= htmlspecialchars(
                                        $photo['caption'] ?: 'Photo #' . $photo['id'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h3>

                                <p>
                                    Uploaded by:
                                    <?= htmlspecialchars(
                                        $photo['nickname'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <p>
                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime($photo['created_at'])
                                    ) ?>
                                </p>

                                <form
                                    action="gallery-delete.php"
                                    method="POST"
                                    onsubmit="return confirm('Delete this photo?')"
                                >

                                    <input
                                        type="hidden"
                                        name="photo_id"
                                        value="<?= (int) $photo['id'] ?>"
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

                                    <button
                                        type="submit"
                                        class="admin-concert__delete"
                                    >
                                        DELETE PHOTO
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <footer class="admin-footer">
            <p>ODETARI FAN HUB / ADMINISTRATION</p>

            <a href="../index.php">BACK TO WEBSITE ↑</a>
        </footer>

    </main>

</body>
</html>