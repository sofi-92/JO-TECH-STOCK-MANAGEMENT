<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php'; 
try {
    $pdo = new PDO("mysql:host=localhost;dbname=jotechdb", "root", "");
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

   
    if ($user && password_verify($password, $user['password'])) {
       
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role']; 

        
        switch ($user['role']) {
            case 'sales':
                header("Location: sales_dashboard.php");
                break;
            case 'purcheser':
                header("Location: purcheser_dashboard.php");
                break;
            case 'storeman':
                header("Location: storeman_dashboard.php");
                break;
            case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
            case 'manager':
                    header("Location: admin_dashboard.php");
            default:
                header("Location: default_dashboard.php"); // Fallback
                break;
        }
        exit();
    } else {
        echo "<script>alert('Invalid email or password!'); window.location.href='login.php';</script>";
        exit();
    }
}
?>