<?php
session_start();

// Include database configuration
require 'config.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];


    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='register_user.php';</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->fetchColumn() > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='register_user.php';</script>";
        exit;
    }

    // Prepare the SQL statement
    $stmt = $pdo->prepare("INSERT INTO users (user_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bindParam(1, $user_name);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $hashed_password);
    $stmt->bindParam(4, $phone);
    $stmt->bindParam(5, $role);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('User registration successful!'); window.location.href='login.php';</script>";
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "<script>alert('Registration failed: " . $errorInfo[2] . "'); window.location.href='register_employee.php';</script>";
    }
}
?>