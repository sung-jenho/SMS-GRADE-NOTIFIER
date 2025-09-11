<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/queries.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        // Try database-backed authentication first
        $user = find_user_by_username($username);
        if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
            $_SESSION['email'] = $user['email'] ?? '';
            $_SESSION['last_activity'] = time();

            header('Location: index.php');
            exit();
        }


        $error_message = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - SMS Grade Notifier</title>
    <link rel="icon" type="image/png" href="../assets/ctu-logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../CSS/login.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" defer></script>
    
</head>
<body>
    <div class="login-container">
        <!-- Left Section - Login Form -->
        <div class="login-form-section">
            <div class="login-header">
                <div id="heyLottie" class="lottie-hey mx-auto mb-3" aria-hidden="true" style="width: 140px; height: 140px;"></div>
                <p class="welcome-subtitle">Please enter your details to access the system</p>
            </div>

            <div class="login-form">
                <?php if ($error_message): ?>
                    <div class="error-message">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" 
                               placeholder="Enter your username" required 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>



                    <button type="submit" class="btn-primary">
                        Sign in
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Section - Gradient Background -->
        <div class="illustration-section">
        </div>

    </div>

    <!-- Loading Animation Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div id="loadingAnimation" class="loading-animation"></div>
        <div class="loading-text">Authenticating<span class="loading-dots"></span></div>
        <div class="loading-subtext">Please wait while we verify your credentials</div>
    </div>

    <script src="../JAVASCRIPTS/login.js"></script>
</body>
</html>
