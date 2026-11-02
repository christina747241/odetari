<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Создаём CSRF-токен, если его ещё нет
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [
    'empty' => 'Please fill in all fields.',
    'current' => 'Current password is incorrect.',
    'length' => 'New password must contain at least 8 characters.',
    'confirm' => 'New passwords do not match.',
    'same' => 'New password must be different from the current one.',
    'save' => 'Could not change the password. Please try again.'
];

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change password</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>
    <main class="edit-page">
        <div class="edit">
            
            <div class="edit__heading">
                <p class="edit__subtitle">ODECORE / ACCOUNT SETTINGS</p>
                
                <h1>Change password</h1>

            </div>

        <?php if (isset($errors[$error])): ?>
            <p class="edit__error">
                <?= htmlspecialchars($errors[$error], ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if (($_GET['updated'] ?? '') === 'success'): ?>
            <p>Password successfully changed!</p>
        <?php endif; ?>

        <form action="actions/password-update.php" method="POST" class="edit__form">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $_SESSION['csrf_token'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <div class="edit__field">

                <p>
                    <label for="current_password">Current password</label>
                </p>
    
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <div class="edit__field">

                <p>
                    <label for="new_password">New password</label>
                </p>
    
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>

            <div class="edit__field">

                <p>
                    <label for="password_confirm">Confirm new password</label>
                </p>
    
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>

            <div class="edit__actions">
                <button type="submit" class="edit__save">Change password</button>
                <a class="edit__back" href="profile.php">Back to profile</a>
            </div>
        </form>


        </div>
    </main>
    

</body>
</html>