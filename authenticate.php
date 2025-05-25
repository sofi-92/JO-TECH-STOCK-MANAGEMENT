<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields";
        header('Location: login.php');
        exit;
    }

    try {
        // Get user from database
        $stmt = $pdo->prepare("SELECT user_id, password, role FROM user WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                case 'manager':
                    header('Location: /AdminDashboard.php');
                    exit;
                case 'Staff':
                    header('Location: /staff.php');
                    exit;
                case 'sales':
                    header('Location: /sales.php');
                    exit;
                case 'procurement':
                    header('Location: /procurement.php');
                    exit;
                default:
                    $_SESSION['error'] = "Unauthorized access level";
                    header('Location: login.php');
                    exit;
            }
        } else {
            $_SESSION['error'] = "Invalid credentials";
        }
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        $_SESSION['error'] = "A system error occurred. Please try again later.";
    } catch (Exception $e) {
        error_log("General error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred. Please try again.";
    }

    header('Location: login.php');
    exit;
}