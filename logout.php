<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out - SmartShop</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta http-equiv="refresh" content="3;url=login.php"> <!-- auto-redirect after 3 seconds -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .logout-container {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logout-container h1 {
            color: #2c3e50;
        }
        .logout-container p {
            margin: 15px 0;
            font-size: 1.1em;
        }
        .logout-container a {
            background: #2980b9;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .logout-container a:hover {
            background: #3498db;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h1><i class="fas fa-sign-out-alt"></i> You have been logged out</h1>
        <p>Thank you for visiting SmartShop.</p>
        <p>Redirecting you to login page...</p>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Go to Login Now</a>
    </div>
</body>
</html>
