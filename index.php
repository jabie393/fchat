<?php
session_start();
include 'config/config.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PHP Chat App</title>

    <!-- bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- box icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <!-- custom styles -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <div class="app-container">
        <div class="hero-section">
            <h1 class="app-title">PHP Chat App <i class='bx bx-chat'></i></h1>

            <?php if ($isLoggedIn): ?>
                <div class="greeting">Hi <b><?= htmlspecialchars($_SESSION['username'] ?? '') ?></b> 👋</div>
            <?php endif; ?>

            <p class="app-description">
                Connect with your friends and colleagues in real-time with our simple and secure chat application.
                <?php if (!$isLoggedIn): ?>
                    Join us now to start chatting!
                <?php endif; ?>
            </p>

            <div class="text-center">
                <?php if ($isLoggedIn): ?>
                    <a href="public/main" class="btn-action btn-primary">
                        <i class='bx bx-chat btn-icon'></i> Go to Chat
                    </a>
                    <a href="public/logout" class="btn-action btn-danger">
                        <i class='bx bx-log-out btn-icon'></i> Logout
                    </a>
                <?php else: ?>
                    <a href="public/login" class="btn-action btn-success">
                        <i class='bx bx-log-in btn-icon'></i> Login
                    </a>
                    <a href="public/register" class="btn-action btn-info">
                        <i class='bx bx-user-plus btn-icon'></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <i class='bx bx-shield-alt feature-icon'></i>
                <h3 class="feature-title">Secure</h3>
                <p class="feature-description">Your conversations are protected with modern security practices.</p>
            </div>
            <div class="feature-card">
                <i class='bx bx-rocket feature-icon'></i>
                <h3 class="feature-title">Fast</h3>
                <p class="feature-description">Real-time messaging with minimal latency for smooth communication.</p>
            </div>
            <div class="feature-card">
                <i class='bx bx-devices feature-icon'></i>
                <h3 class="feature-title">Responsive</h3>
                <p class="feature-description">Works perfectly on all devices, from mobile to desktop.</p>
            </div>
        </div>

    </div>

    <footer>
        <p class="footer-text">© <?php echo date('Y'); ?> PHP Chat App. All rights reserved.</p>
    </footer>

</body>

</html>