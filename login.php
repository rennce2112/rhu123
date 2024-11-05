<?php
session_start();

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    switch ($_SESSION['user_type']) {
        case 'admin':
            header("Location: server/admin/admin_create_operator.php");
            break;
        case 'operator':
            header("Location: operator_dashboard.php");
            break;
        case 'user':
            header("Location: server/protected_page.php");
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RHU System</title>
    <link rel="stylesheet" href="navigation.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-btn {
            background-color: #ff9900;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #e68a00;
        }

        .error-message {
            background-color: #ffe6e6;
            color: #cc0000;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container container">
            <input type="checkbox" name="" id="">
            <div class="hamburger-lines">
                <span class="line line1"></span>
                <span class="line line2"></span>
                <span class="line line3"></span>
            </div>
            <ul class="menu-items">
                <li><a href="index.php">Home</a></li>
                <li><a href="collaborators.php">Collaborators</a></li>
                <li><a href="category.php">Category</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
            <h1 class="logo">HEALTH</h1>
        </div>
    </nav>

    <div class="login-container">
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                $error = htmlspecialchars($_GET['error']);
                echo $error === 'invalid' ? 'Invalid email or password.' : $error;
                ?>
            </div>
        <?php endif; ?>

        <form class="login-form" action="server/login_process.php" method="POST">
            <div class="form-group">
                <label for="email_address">Email Address</label>
                <input type="email" name="email_address" id="email_address" required 
                       placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required 
                       placeholder="Enter your password">
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>