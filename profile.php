<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

// Получаем бронирования текущего пользователя
$stmt = $pdo->prepare(
    'SELECT bookings.id AS booking_id,
            bookings.quantity,
            concerts.title,
            concerts.description,
            concerts.image,
            concerts.event_date,
            concerts.location
    FROM bookings
    JOIN concerts ON bookings.concert_id = concerts.id
    WHERE bookings.user_id = ?'
);

$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>

<header class="header">
    <div class="container">

    <div class="content">

        <a href="#!">
            <img class="header__img" src="img/logo.jpg" alt="logo">
        </a>
    
        <nav>
            <a href="index.php">Home</a>
        </nav>
        <a class="header__profile" href="logout.php">Log out</a>
    </div>

    </div>
    
</header>

<main>
    <section class="profile">
        <div class="container">

        <div class="profile__data">

            <img class="profile__img"
                src="<?= htmlspecialchars(
                    $_SESSION['photo'] ?? 'img/profile.jpg',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt="Profile photo"
            >

            <?php if (($_GET['updated'] ?? '') === 'success'): ?>
                <p>Profile successfully updated!</p>
            <?php endif; ?>

            <div class="profile__desc">

                <h2 class="profile__name">
                    <?= htmlspecialchars($_SESSION['nickname'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="profile__subtitle">Im listening odetari since 2023</p>
            </div>
            <div class="profile__btns">
                <a class="profile__btn" href="profile-edit.php">Edit</a>
                <a class="profile__btn profile__btn--change" href="change-password.php">Change pass</a>
            </div>
        </div>

        <div class="booking">
            <h3>Your bookings</h3>

            <?php if (($_GET['booking_cancelled'] ?? '') === 'success'): ?>

                <p>Booking successfully cancelled!</p>

            <?php elseif (($_GET['booking_cancelled'] ?? '') === 'error'): ?>

                <p>Could not cancel the booking. Please try again.</p>

            <?php endif; ?>

            <?php if (empty($bookings)): ?>

            <p>You don't have any bookings yet.</p>

            <?php else: ?>

                <?php foreach ($bookings as $booking): ?>

                    <div class="booking__data">

                        <img
                            src="<?= htmlspecialchars($booking['image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($booking['title'], ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div class="booking__text">

                            <p>
                                <?= htmlspecialchars($booking['title'], ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p>
                                <?= date('d.m.Y H:i', strtotime($booking['event_date'])) ?>
                            </p>

                            <p>
                                <?= htmlspecialchars($booking['location'], ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p>
                                <?= htmlspecialchars($booking['description'], ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p>Tickets: <?= (int) $booking['quantity'] ?></p>

                            <form
                                action="actions/booking-cancel.php"
                                method="POST"
                                class="booking__cancel-form"
                            >
                                <input
                                    type="hidden"
                                    name="booking_id"
                                    value="<?= (int) $booking['booking_id'] ?>"
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

                                <button type="submit" class="profile__btn">
                                    Cancel booking
                                </button>
                            </form>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
            
        </div>
    </section>
</main>

<footer class="footer">
    <p>christina@2026</p>
    <a href="#!">
        <img src="img/footer/instagram.png" alt="insta">
    </a>
    <a href="#!">
        <img src="img/footer/whatsapp.png" alt="whatsapp">
    </a>
    <a href="#!">
        <img src="img/footer/twitter.png" alt="x">
    </a>
    <a href="#!">
        <img src="img/footer/telegram.png" alt="tg">
    </a>
</footer>
</body>
</html>