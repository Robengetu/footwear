<?php
session_start();
require_once '../database/connection.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function login($username, $password) {
    try {
        // Check user credentials in database
        $user = fetchOne("SELECT id, username, password FROM users WHERE username = ?", [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit;
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username']
        ];
    }
    return null;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (login($username, $password)) {
            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    }
    
    if ($action === 'logout') {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
    
    if ($action === 'register') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $email = $_POST['email'] ?? '';
        
        try {
            // Validate input
            if (strlen($username) < 3) {
                echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
                exit;
            }
            
            if (strlen($password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
                exit;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
                exit;
            }
            
            // Check if username already exists
            $existing = fetchOne("SELECT id FROM users WHERE username = ?", [$username]);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Username already exists']);
                exit;
            }
            
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            executeQuery("INSERT INTO users (username, password, email) VALUES (?, ?, ?)", 
                        [$username, $hashed_password, $email]);
            
            // Get the new user ID and log them in
            $user = fetchOne("SELECT id, username FROM users WHERE username = ?", [$username]);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    }
}
?>
