<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Создаём CSRF-токен
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/config/database.php';

// Получаем данные пользователя
$stmt = $pdo->prepare(
    'SELECT nickname, email, photo
     FROM users
     WHERE id = ?'
);

$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    exit('User not found');
}

// Сообщения об ошибках
$errors = [
    'nickname' => 'Nickname must contain 2–50 characters.',
    'too_large' => 'Photo must not exceed 5 MB.',
    'type' => 'Please upload a JPG, PNG or WEBP image.',
    'upload' => 'Photo upload failed. Please try again.',
    'save' => 'Could not save profile changes.'
];

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit profile</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>

    <main class="edit-page">

        <div class="edit">

            <div class="edit__heading">
                <p class="edit__subtitle">ODECORE / ACCOUNT SETTINGS</p>

                <h1>Edit profile</h1>

                <p class="edit__description">
                    Customize your profile and show your vibe.
                </p>
            </div>

            <?php if (isset($errors[$error])): ?>

                <div class="edit__error">
                    <?= htmlspecialchars($errors[$error], ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>

            <form
                class="edit__form"
                action="actions/profile-update.php"
                method="POST"
                enctype="multipart/form-data"
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

                <!-- Фотография профиля -->
                <div class="edit__photo">

                    <img
                        src="<?= htmlspecialchars(
                            $user['photo'] ?: 'img/profile.jpg',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        alt="Profile photo"
                    >

                    <div class="edit__photo-text">
                        <h3>YOUR PROFILE PHOTO</h3>
                        <p>Choose an image that represents you.</p>
                    </div>

                </div>

                <!-- Никнейм -->
                <div class="edit__field">

                    <label for="nickname">Nickname</label>

                    <input
                        type="text"
                        id="nickname"
                        name="nickname"
                        value="<?= htmlspecialchars(
                            $user['nickname'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="50"
                        required
                    >

                </div>

                <!-- Email -->
                <div class="edit__field">

                    <label for="email">Email address</label>

                    <input
                        type="email"
                        id="email"
                        value="<?= htmlspecialchars(
                            $user['email'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        disabled
                    >

                    <p class="edit__hint">
                        Email address cannot be changed.
                    </p>

                </div>

                <!-- Новая фотография -->
                <div class="edit__field">

                    <label for="photo">New profile photo</label>

                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <p class="edit__hint">
                        JPG, PNG or WEBP. Maximum size: 5 MB.
                    </p>

                </div>

                <!-- Кнопки -->
                <div class="edit__actions">

                    <button type="submit" class="edit__save">
                        Save changes
                    </button>

                    <a href="profile.php" class="edit__back">
                        Back to profile
                    </a>

                </div>

            </form>

        </div>

    </main>

</body>
</html>