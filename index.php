<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('images/login background.png'); /* Full background image */
            background-size: cover; /* Cover the entire background */
            background-position: center; /* Center the image */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full height */
        }

        .login-container {
            position: absolute;
            width: 370px; /* Max width for the form */
            height: 400px;
            background:rgba(255, 255, 255, 0.9); /* Slightly transparent white */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 40px; /* Padding for the form area */
            filter: drop-shadow(0px 2.84583px 49.8021px rgba(0, 0, 0, 0.1));
            transform: matrix(1, 0, 0.01, 1, 0, 0);
        }

/* Loin 

position: absolute;
width: 455px;
height: 600px;
left: 511.5px;
top: 93.86px;

filter: drop-shadow(0px 2.84583px 49.8021px rgba(0, 0, 0, 0.1));
transform: matrix(1, 0, 0.01, 1, 0, 0);

*/

        .logo {
            display: block;
            margin: 0 auto 20px auto; /* Center the logo */
            width: 150px; /* Adjust size as needed */
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px; /* Spacing below the title */
        }

        .form-group {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }

        .form-group label {
            width: 30%;
            font-weight: 600;
            color: #555;
        }

        .form-group input {
            width: 70%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #0a888f;
            outline: none;
            box-shadow: 0 0 0 2px rgba(10, 136, 143, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4f46e5; /* Teal color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: rgb(4, 70, 73);
        }

        p {
            text-align: center;
            margin-top: 15px;
        }

        a {
            color: #0a888f;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="images/Qoricha logo.png" alt="JO TECH Logo" class="logo"> <!-- Logo -->
        <h2>Welcome Back!</h2>
        <form action="authenticate.php" method="post" class="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p><a href="/forgot-password">Forgot Password?</a></p>
    </div>
</body>
</html>