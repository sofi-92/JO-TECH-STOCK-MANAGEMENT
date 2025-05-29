<?php
// Start session
session_start();

// Include database configuration
require_once 'config.php';

// Initialize variables
$errors = [];
$success = false;
$user_name = $email = $phone = $role = '';

// Process form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get database connection


    // Sanitize inputs
    $user_name = htmlspecialchars(trim($_POST['user_name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $role = htmlspecialchars(trim($_POST['role'] ?? ''));
    $created_at = date('Y-m-d');

    // Validate inputs
    if (empty($user_name)) {
        $errors['user_name'] = "Full name is required";
    } elseif (strlen($user_name) > 100) {
        $errors['user_name'] = "Name must be less than 100 characters";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } elseif (strlen($email) > 100) {
        $errors['email'] = "Email must be less than 100 characters";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email already registered";
        }
        $stmt->close();
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    if (empty($phone)) {
        $errors['phone'] = "Phone number is required";
    }
  /*   } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format";
    } */

    if (empty($role)) {
        $errors['role'] = "Please select a role";
    } elseif (!in_array($role, ['manager', 'purcheser', 'storeman', 'sales'])) {
        $errors['role'] = "Invalid role selected";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert into database using prepared statement
        $stmt = $conn->prepare("INSERT INTO users (user_name, email, password, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user_name, $email, $hashed_password, $phone, $role, $created_at);
        
        if ($stmt->execute()) {
            $success = true;
            // Clear form fields
            $user_name = $email = $phone = $role = '';
            $_SESSION['registration_success'] = true;
            header("Location: login.php");
            exit();
        } else {
            $errors['database'] = "Registration failed: " . $conn->error;
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
    <title>Register -</title>
    <style>
              :root {
            --primary-color: #0a888f;
            --primary-dark: #074e52;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --border-color: #ddd;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .form-container:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-color);
            font-size: 28px;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            transition: var(--transition);
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 136, 143, 0.2);
        }

        /* Role Selection Grid */
        .role-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .role-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .role-option {
            padding: 15px;
            background: var(--secondary-color);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .role-option:hover {
            background: #e9ecef;
        }

        .role-option input[type="radio"] {
            margin-right: 8px;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: mediumblue;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        button:hover {
            background-color: black;
            transform: translateY(-2px);
        }

        p {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }


input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        .success {
            color: #2ecc71;
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        
        <?php if (!empty($errors['database'])): ?>
            <div class="error"><?php echo $errors['database']; ?></div>
        <?php endif; ?>
        
        <form action="register.php" method="post">
            <div class="form-row">
                <div class="column">
                    <input type="text" name="user_name" placeholder="Full name" value="<?php echo htmlspecialchars($user_name); ?>" required>
                    <?php if (!empty($errors['user_name'])): ?>
                        <span class="error"><?php echo $errors['user_name']; ?></span>
                    <?php endif; ?>
                    
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                    
                    <input type="password" name="password" placeholder="Password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                    
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <span class="error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                    
                    <input type="text" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone); ?>" required>
                    <?php if (!empty($errors['phone'])): ?>
                        <span class="error"><?php echo $errors['phone']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-row">
                <input type="text" name="created_at" placeholder="Joining Date" value="<?php echo date('Y-m-d'); ?>" readonly>
            </div>
            
            <!-- Role Selection Grid -->
            <div class="role-grid">
                <!-- Left Column -->
                <div class="role-column">
                    <label class="role-option">
                        <input type="radio" name="role" value="manager" <?php echo ($role === 'manager') ? 'checked' : ''; ?> required> Manager
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="purcheser" <?php echo ($role === 'purcheser') ? 'checked' : ''; ?> required> Purchaser
                    </label>
                </div>
                
                <!-- Right Column -->
                <div class="role-column">
                    <label class="role-option">
                        <input type="radio" name="role" value="storeman" <?php echo ($role === 'storeman') ? 'checked' : ''; ?> required> Storeman
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="sales" <?php echo ($role === 'sales') ? 'checked' : ''; ?> required> Sales
                    </label>
                </div>
            </div>
            <?php if (!empty($errors['role'])): ?>
                <div class="error" style="text-align: center;"><?php echo $errors['role']; ?></div>
            <?php endif; ?>
            
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>