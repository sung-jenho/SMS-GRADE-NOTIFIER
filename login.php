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
    require_once __DIR__ . '/includes/db.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        $mysqli = get_db_connection();
        
        // For demo purposes, using a simple admin account
        // In production, you should hash passwords and use proper authentication
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'admin';
            $_SESSION['full_name'] = 'Administrator';
            $_SESSION['email'] = 'admin@ctu.edu.ph';
            
            header('Location: index.php');
            exit();
        } else {
            $error_message = 'Invalid username or password.';
        }
        
        $mysqli->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - SMS Grade Notifier</title>
    <link rel="icon" type="image/png" href="assets/ctu-logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
        }

        /* Left Section - Login Form */
        .login-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #ffffff;
            position: relative;
        }

        .login-header {
            margin-bottom: 32px;
            text-align: center;
            width: 100%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
        }

        .logo-icon img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.01em;
        }

        .welcome-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
            line-height: 1.2;
            letter-spacing: -0.02em;
            text-align: center;
        }

        .welcome-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 400;
            line-height: 1.4;
            text-align: center;
        }

        .login-form {
            max-width: 360px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 400;
            color: #111827;
            background: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }



        .btn-primary {
            width: 100%;
            padding: 14px 20px;
            background: #2563eb;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
            margin-top: 8px;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
        }



        /* Right Section - Illustration */
        .illustration-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .illustration-content {
            text-align: center;
            color: white;
            position: relative;
            z-index: 2;
        }

        .illustration-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .illustration-subtitle {
            font-size: 0.875rem;
            font-weight: 400;
            opacity: 0.9;
            line-height: 1.4;
            max-width: 320px;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 60px;
            height: 60px;
            top: 25%;
            left: 20%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 40px;
            height: 40px;
            top: 65%;
            right: 25%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 30px;
            height: 30px;
            bottom: 35%;
            left: 30%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Error Message */
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-message i {
            font-size: 0.875rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .login-form-section {
                padding: 32px;
            }
            
            .illustration-section {
                padding: 32px;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-form-section {
                padding: 32px 20px;
                order: 2;
            }
            
            .illustration-section {
                padding: 40px 20px;
                order: 1;
                min-height: 200px;
            }
            
            .welcome-title {
                font-size: 1.5rem;
            }
            
            .illustration-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .login-form-section {
                padding: 24px 16px;
            }
            
            .illustration-section {
                padding: 32px 16px;
                min-height: 180px;
            }
            
            .welcome-title {
                font-size: 1.375rem;
            }
            
            .illustration-title {
                font-size: 1.375rem;
            }
            
            .brand-logo {
                margin-bottom: 16px;
            }
            
            .login-header {
                margin-bottom: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Section - Login Form -->
        <div class="login-form-section">
            <div class="login-header">
                <div class="brand-logo">
                    <div class="logo-icon">
                        <img src="assets/ctu-logo.png" alt="CTU Logo">
                    </div>
                    <span class="brand-name">SMS Grade Notifier</span>
                </div>
                
                <h1 class="welcome-title">Welcome back</h1>
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
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Enter your password" required>
                    </div>



                    <button type="submit" class="btn-primary">
                        Sign in
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Section - Illustration -->
        <div class="illustration-section">
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            
            <div class="illustration-content">
                <h2 class="illustration-title">Manage Grades</h2>
                <p class="illustration-subtitle">
                    Streamline your academic processes with our comprehensive SMS Grade Notifier system. 
                    Send instant notifications to parents and students.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Add loading state to form submission
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-primary');
            
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = 'Signing in...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>
