<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/config/database.php';

$stmt = $pdo->query(
    'SELECT * FROM concerts ORDER BY event_date ASC'
);

$concerts = $stmt->fetchAll();

$stmt = $pdo->query(
    'SELECT gallery_photos.id,
            gallery_photos.image_path,
            gallery_photos.caption,
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
    <title>ODETARI</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@14.3.0/swiper-bundle.min.css">

    <link rel="stylesheet" href="css/main.css">
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
            <a href="#about">About</a>
            <a href="#discography">Discography</a>
            <a href="#concerts">Concerts</a>
            <a href="#gallery">Gallery</a>
            <a class="header__profile" href="profile.php">Profile</a>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="admin/index.php">Admin panel</a>
            <?php endif; ?>
        </nav>
    </div>

    </div>
    
</header>

<main>

    <section class="main" id="main">

        <div class="main__text">

        <div class="text">
            <p class="main__description accent">A different frequency.</p>
            <p class="main__description accent">A different reality.</p>
            <p class="main__description accent">Step into the sound.</p>
        </div>

            <p class="main__quote accent">TOO LOUD TO FEEL NOTHING.</p>
        </div>
    
        <div class="text__accent">
            <div class="text__accent-df">

                <div class="main__btn">
                    <img src="img/stroke2.png" alt="">
                </div>

                
                    <p class="main__para">ENTER THE VOID</p>
                

            </div>
        </div>

        <h2 class="main__subtitle">WELCOME TO THE WORLD OF</h2>
        <h1 class="main__title">ODETARI</h1>

        <img class="main__img" src="img/main-photo.png" alt="odetari">

    </section>

    <section class="about" id="about">
        <div class="container">

            <h4 class="title accent">REALITY IS OVERRATED.</h4>
    
            <div class="about__content">

                <img src="img/about.jpg" alt="odetari">
        
                <div class="about__desc">

                    <h3>WHO IS ODETARI?</h3>
                    <p>Odetari is an American rapper, singer, and music producer known for his unique blend of electronic beats, energetic rhythms, and dark digital aesthetics. His music combines elements of rage, EDM, and experimental sounds, creating an intense atmosphere that feels like stepping into another world. With his distinctive style and creative vision, Odetari continues to push the boundaries of modern music.Odetari is an American rapper, singer, and music producer known for his unique blend of electronic beats, energetic rhythms, and dark digital aesthetics. His music combines elements of rage, EDM, and experimental sounds,</p>
                    <p>One sound. A thousand emotions. No limits.</p>
                </div>

            </div>

        </div>
    </section>

    <section class="discography" id="discography">

        <div class="container">
            <h3 class="title">WELCOME TO ODECORE</h3>
        </div>

        <div class="swiper album-swiper">

            <div class="swiper-wrapper">

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album1.png"
                            alt="Обложка релиза 1">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 01</p>
                            <h4>Название релиза 1</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album2.png"
                            alt="Обложка релиза 2">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 02</p>
                            <h4>Название релиза 2</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album3.png"
                            alt="Обложка релиза 3">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 03</p>
                            <h4>Название релиза 3</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album4.png"
                            alt="Обложка релиза 4">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 04</p>
                            <h4>Название релиза 4</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album5.png"
                            alt="Обложка релиза 5">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 05</p>
                            <h4>Название релиза 5</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album6.png"
                            alt="Обложка релиза 6">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 06</p>
                            <h4>Название релиза 6</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album7.png"
                            alt="Обложка релиза 7">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 07</p>
                            <h4>Название релиза 7</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album8.png"
                            alt="Обложка релиза 8">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 08</p>
                            <h4>Название релиза 8</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album9.png"
                            alt="Обложка релиза 9">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 09</p>
                            <h4>Название релиза 9</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album10.png"
                            alt="Обложка релиза 10">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 10</p>
                            <h4>Название релиза 10</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album11.png"
                            alt="Обложка релиза 11">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 11</p>
                            <h4>Название релиза 11</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album12.png"
                            alt="Обложка релиза 12">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 12</p>
                            <h4>Название релиза 12</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album13.png"
                            alt="Обложка релиза 13">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 13</p>
                            <h4>Название релиза 13</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album14.png"
                            alt="Обложка релиза 14">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 14</p>
                            <h4>Название релиза 14</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album15.png"
                            alt="Обложка релиза 15">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 15</p>
                            <h4>Название релиза 15</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album16.png"
                            alt="Обложка релиза 16">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 16</p>
                            <h4>Название релиза 16</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album17.png"
                            alt="Обложка релиза 17">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 17</p>
                            <h4>Название релиза 17</h4>
                        </div>
                    </article>
                </div>

                <div class="swiper-slide">
                    <article class="album-card" tabindex="0">
                        <img src="img/dyscography/album18.png"
                            alt="Обложка релиза 18">
                        <div class="album-info">
                            <p>ODECORE ARCHIVE / 18</p>
                            <h4>Название релиза 18</h4>
                        </div>
                    </article>
                </div>

            </div>

            <button class="swiper-button-prev album-swiper-prev"
                    type="button"
                    aria-label="Предыдущий релиз"></button>

            <button class="swiper-button-next album-swiper-next"
                    type="button"
                    aria-label="Следующий релиз"></button>

        </div>

        <div class="container">
            <h4 class="dysco__title accent">LOUD MUSIC. QUIET THOUGHTS.</h4>
        </div>

    </section>

    <section id="concerts" class="concerts">
        <div class="container">

            <!-- Заголовок и стрелки -->
            <div class="concerts__heading">
                <div>
                    <p class="concerts__eyebrow">ODECORE / LIVE EVENTS</p>
                    <h3>UPCOMING CONCERTS</h3>
                    <h4>EXPERIENCE THE ENERGY</h4>
                </div>

                <div class="concerts__controls">
                    <button
                        class="concerts__arrow concerts__prev"
                        type="button"
                        aria-label="Previous concert"
                    >
                        &#8592;
                    </button>

                    <button
                        class="concerts__arrow concerts__next"
                        type="button"
                        aria-label="Next concert"
                    >
                        &#8594;
                    </button>
                </div>
            </div>

            <?php if (empty($concerts)): ?>

                <p>No upcoming concerts yet.</p>

            <?php else: ?>

                <!-- Основной контейнер Swiper -->
                <div class="swiper concert-swiper">

                    <!-- Обертка всех слайдов -->
                    <div class="swiper-wrapper">

                        <?php foreach ($concerts as $index => $concert): ?>

                            <!-- Один слайд -->
                            <div class="swiper-slide">

                                <article class="concert-card">

                                    <!-- Фото концерта -->
                                    <div class="concert-card__image">

                                        <img
                                            src="<?= htmlspecialchars($concert['image'], ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($concert['title'], ENT_QUOTES, 'UTF-8') ?>"
                                        >

                                        <span>
                                            <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?>
                                        </span>

                                    </div>

                                    <!-- Информация о концерте -->
                                    <div class="concert-card__content">

                                        <p class="concert-card__label">
                                            UPCOMING EVENT
                                        </p>

                                        <h4>
                                            <?= htmlspecialchars($concert['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </h4>

                                        <p class="concert-card__description">
                                            <?= htmlspecialchars($concert['description'], ENT_QUOTES, 'UTF-8') ?>
                                        </p>

                                        <p class="concert-card__description">
                                            <?= htmlspecialchars($concert['location'], ENT_QUOTES, 'UTF-8') ?>
                                        </p>

                                        <p class="concert-card__description">
                                            <?= date('d.m.Y H:i', strtotime($concert['event_date'])) ?>
                                        </p>

                                        <a
                                            class="card__link"
                                            href="concert.php?id=<?= (int) $concert['id'] ?>"
                                        >
                                            Learn more <span>&#8594;</span>
                                        </a>

                                    </div>

                                </article>

                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Нижняя часть слайдера и индикаторы -->
                <div class="concerts__footer">

                    <p>NO GENRE. NO LIMITS.</p>

                    <div class="concerts__pagination"></div>

                </div>

            <?php endif; ?>

        </div>
    </section>

    <section class="gallery" id="gallery">
        <div class="container">
            <h4 class="accent">NO GENRE. NO LIMITS.</h4>
        </div>

            <div class="photos">

                <?php foreach ($galleryPhotos as $photo): ?>

                    <figure class="photos__item">

                        <img
                            src="<?= htmlspecialchars(
                                $photo['image_path'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $photo['caption'] ?: 'Odetari fan photo',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            loading="lazy"
                        >

                        <figcaption>
                            <?php if (!empty($photo['caption'])): ?>

                                <?= htmlspecialchars(
                                    $photo['caption'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            <?php else: ?>

                                By <?= htmlspecialchars(
                                    $photo['nickname'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            <?php endif; ?>
                        </figcaption>

                    </figure>

                <?php endforeach; ?>

                <figure class="photos__item">
                    <img src="img/gallery/gallery1.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 001</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery2.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 002</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery3.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 003</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery4.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 004</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery5.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 005</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery6.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 006</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery7.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 007</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery8.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 008</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery9.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 009</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery10.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 010</figcaption>
                </figure>

                <figure class="photos__item">
                    <img src="img/gallery/gallery11.jpg"
                        alt="Фотография из архива Odetari"
                        loading="lazy">
                    <figcaption>ARCHIVE / 011</figcaption>
                </figure>

            </div>
    
            <div class="container">

                <?php if (($_GET['upload'] ?? '') === 'success'): ?>
                    <p>Photo successfully uploaded!</p>

                <?php elseif (($_GET['upload'] ?? '') === 'too_large'): ?>
                    <p>Photo must not exceed 5 MB.</p>

                <?php elseif (($_GET['upload'] ?? '') === 'type'): ?>
                    <p>Please upload a JPG, PNG or WEBP image.</p>

                <?php elseif (($_GET['upload'] ?? '') === 'error'): ?>
                    <p>Upload failed. Please try again.</p>

                <?php endif; ?>


                <?php if (isset($_SESSION['user_id'])): ?>

                    <form
                        action="actions/gallery-upload.php"
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

                        <label for="gallery-photo">Choose a photo</label>

                        <input
                            type="file"
                            id="gallery-photo"
                            name="photo"
                            accept="image/jpeg,image/png,image/webp"
                            required
                        >

                        <input
                            type="text"
                            name="caption"
                            maxlength="150"
                            placeholder="Photo caption (optional)"
                        >

                        <button class="gallery__btn" type="submit">
                            share your vibe
                        </button>

                    </form>

                <?php else: ?>

                    <a href="login.php" class="gallery__btn">
                        log in to share your photo
                    </a>

                <?php endif; ?>
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

<script
    src="https://cdn.jsdelivr.net/npm/swiper@14.3.0/swiper-bundle.min.js"
    defer
></script>

<script src="js/script.js"></script>
<script src="js/albums-slider.js" defer></script>
    
</body>
</html>