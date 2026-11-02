<?php

session_start();

//создаем csrf-токен для защиты формы
if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Получаем сообщения от обработчика
$errors = $_SESSION['register_errors'] ?? [];
$oldInput = $_SESSION['register_old'] ?? [];
$success = $_SESSION['register_success'] ?? '';

unset(
    $_SESSION['register_errors'],
    $_SESSION['register_old'],
    $_SESSION['register_success']
);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<header class="header">
    <div class="container">

    <div class="content">

        <a href="#!">
            <img class="header__img" src="img/logo.jpg" alt="logo">
        </a>

    </div>

    </div>
    
</header>

<main>
    <h1 class="title">Create account</h1>

    <?php if(!empty($success)): ?>
        <p class="form__success" role="status">
            <?= htmlspecialchars(
                $success,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <?php if(!empty($errors)): ?>
        <ul class="form__errors" role="alert">

            <?php foreach ($errors as $error): ?>
                <li>
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </li>
        <?php endforeach ?>
        </ul>
    <?php endif;?>

    <form action="actions/register-process.php" method="POST" class="form">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <label for="nickname">Nickname</label>
        <input
            id="nickname"
            name="nickname"
            type="text"
            placeholder="Enter your nickname"
            value="<?= htmlspecialchars(
                $oldInput['nickname'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            minlength="2"
            maxlength="50"
            autocomplete="nickname"
            required
        >

        <label for="email">Email</label>
        <input
            id="email"
            name="email"
            type="email"
            placeholder="Enter your email"
            value="<?= htmlspecialchars(
                $oldInput['email'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            maxlength="255"
            autocomplete="email"
            required
        >

        <label for="password">Password</label>
        <input
            id="password"
            name="password"
            type="password"
            placeholder="Create a password"
            minlength="8"
            maxlength="72"
            autocomplete="new-password"
            required
        >

        <label for="password_confirm">
            Confirm password
        </label>
        <input
            id="password_confirm"
            name="password_confirm"
            type="password"
            placeholder="Repeat your password"
            minlength="8"
            maxlength="72"
            autocomplete="new-password"
            required
        >

        <button class="form__btn" type="submit">
            Create account
        </button>
    </form>

    <p class="auth__switch">
        Already have an account?
        <a href="login.php">Log in</a>
    </p>


</main>
    
</body>
</html>