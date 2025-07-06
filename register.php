<?php
session_start();
require_once 'database/connection.php';
// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        require_once 'database/connection.php'; // Make sure this is at the top of the file if not already
    
        // Check if username or email already exists
        $existingUser = fetchOne("SELECT * FROM users WHERE email = ? OR username = ?", [$email, $username]);
    
        if ($existingUser) {
            $error = 'Email or username already exists';
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
            // Insert the new user into database
            executeQuery("INSERT INTO users (username, email, password) VALUES (?, ?, ?)", [$username, $email, $passwordHash]);
    
            $success = 'Registration successful! You can now login.';
            header("refresh:2;url=login.php");
        }
    }
}    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bits Footwear</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-form">
            <div class="auth-header">
                <h1><i class="fas fa-shoe-prints"></i> BITS FOOTWEAR</h1>
                <p>Create your account to start shopping.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="auth-btn">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
