<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/adlogin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo"><image src="./image/logooo2.jpg">
            <span>Charity Donation Management</span>
        </div>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="about.php"><i class="fas fa-building"></i>About</a></li>
            <li><a href="news.php"><i class="fas fa-newspaper"></i>News</a></li>
            <li><a href="donate.php"><i class="fas fa-donate"></i>Donate</a></li>
            <li class="active"><a href="adlogin.php"><i class="fas fa-sign-in-alt"></i>Login</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <div class="login-container">
        <h2>Admin Login</h2>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <?php session_start(); ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message" style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
