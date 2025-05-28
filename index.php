<?php
session_start();
require_once 'config.php';

$error = '';

// Redirect logged-in users away from login page
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header("Location: AdminDashboard.php");
            break;
        case 'manager':
            header("Location: AdminDashboard.php");
            break;
        case 'staff':
            header("Location: StaffDashboard.php");
            break;
        case 'sales':
            header("Location: SalesDashboard.php");
            break;
        case 'procurement':
            header("Location: ProcurementDashboard.php");
            break;
        default:
            // If role doesn't match, clear session and stay on login
            session_destroy();
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $conn->prepare("SELECT user_name, user_id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['user_name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Immediately redirect after successful login
                switch ($user['role']) {
                    case 'admin':
                        header("Location: AdminDashboard.php");
                        break;
                    case 'manager':
                        header("Location: AdminDashboard.php");
                        break;
                    case 'staff':
                        header("Location: StaffDashboard.php");
                        break;
                    case 'sales':
                        header("Location: SalesDashboard.php");
                        break;
                    case 'procurement':
                        header("Location: ProcurementDashboard.php");
                        break;
                    default:
                        header("Location: login.php");
                }
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JO TECH STOCK MANAGEMENT </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg,rgb(9, 71, 87), #0d47a1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .background-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 15s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(10px, 20px); }
            50% { transform: translate(-20px, 10px); }
            75% { transform: translate(-10px, -20px); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        h1 {
            color: #fff;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem;
        }

        .input-group {
            margin-bottom: 30px;
            position: relative;
        }

        input {
            width: 100%;
            padding: 15px 20px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 15px rgba(0,230,118,0.3);
        }

        label {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.6);
            pointer-events: none;
            transition: all 0.3s ease;
        }

        input:focus ~ label,
        input:valid ~ label {
            top: -10px;
            left: 10px;
            font-size: 0.8rem;
            color: #00e676;
        }

        button {
            width: 100%;
            padding: 15px;
            background: #00e676;
            border: none;
            border-radius: 8px;
            color: #000;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,230,118,0.3);
        }

        .error-message {
            color: #ff4444;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .additional-links {
            text-align: center;
            margin-top: 25px;
            color: rgba(255,255,255,0.8);
        }

        .additional-links a {
            color: #00e676;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .additional-links a:hover {
            opacity: 0.8;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="background-shapes">
        <div class="shape" style="width: 250px; height: 250px; top: 20%; left: 15%"></div>
        <div class="shape" style="width: 180px; height: 180px; top: 65%; right: 20%"></div>
    </div>

    <div class="login-container">
        <h1><i class="fas fa-sign-in-alt"></i> Welcome Back</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="input-group">
                <input type="text" name="username" required>
                <label>Username</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <button type="submit">
                <i class="fas fa-unlock-alt"></i> Login
            </button>
        </form>

        <div class="additional-links">
            <p>Need help? <a href="reset_password.php">Reset Password</a></p>
        </div>
    </div>
</body>
</html>