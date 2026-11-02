<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/config/database.php';

// Получаем ID концерта из адресной строки
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Проверяем ID
if (!$id) {
    exit('Concert not found');
}

// Получаем концерт из базы данных
$stmt = $pdo->prepare(
    'SELECT * FROM concerts WHERE id = ?'
);

$stmt->execute([$id]);

$concert = $stmt->fetch();

// Проверяем, существует ли концерт
if (!$concert) {
    exit('Concert not found');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert</title>

    <link rel="stylesheet" href="css/concert.css">
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
            <a class="header__profile" href="profile.php">Profile</a>
        </div>

        </div>
        
    </header>
    <main>
        <section>
                <div class="concert">

                    <img
                        src="<?= htmlspecialchars($concert['image'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($concert['title'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                    
                    <div class="concert__desc">
                        
                        <h1>
                            <?= htmlspecialchars($concert['title'], ENT_QUOTES, 'UTF-8') ?>
                        </h1>
                        <p>
                            <?= htmlspecialchars($concert['description'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p>
                            <?= date('d.m.Y H:i', strtotime($concert['event_date'])) ?>
                        </p>

                        <p>
                            <?= htmlspecialchars($concert['location'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <?php
                            $status = $_GET['booking'] ?? '';

                            if ($status === 'success'): ?>

                                <p class="attention">Tickets successfully booked!</p>

                            <?php elseif ($status === 'full'): ?>

                                <p class="attention">Sorry, there are not enough tickets available.</p>

                            <?php elseif ($status === 'error'): ?>

                                <p class="attention">Something went wrong. Please try again.</p>

                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_id'])): ?>

                            <form action="actions/booking-process.php" method="POST">

                                <input
                                    type="hidden"
                                    name="concert_id"
                                    value="<?= (int) $concert['id'] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                                >

                                <label for="quantity">Number of tickets</label>

                                <input
                                    type="number"
                                    id="quantity"
                                    name="quantity"
                                    min="1"
                                    max="10"
                                    value="1"
                                    required
                                >

                                <button type="submit" class="concert__btn">
                                    Book tickets
                                </button>

                            </form>

                        <?php else: ?>

                        <a href="login.php" class="concert__btn">
                            Log in to book tickets
                        </a>

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