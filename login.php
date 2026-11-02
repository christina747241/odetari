<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
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

<h1 class="title">Log in</h1>

<form action="actions/login-process.php" method="POST" class="form">

    <label for="email">Email</label>
    <input
        id="email"
        name="email"
        type="email"
        placeholder="Enter your email"
        required
    >

    <label for="password">Password</label>
    <input
        id="password"
        name="password"
        type="password"
        placeholder="Enter your password"
        required
    >

    <button class="form__btn" type="submit">
        Log in
    </button>

</form>



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