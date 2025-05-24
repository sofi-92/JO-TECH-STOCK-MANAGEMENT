<?php
// login.php
session_start();

// Demo accounts data
$demoAccounts = [
    'admin' => 'admin123',
    'manager' => 'manager123',
    'staff' => 'staff123',
    'sales' => 'sales123',
    'procurement' => 'proc123'
];

$error = '';
$isLoading = false;

// Check if user is already logged in and redirect
if (isset($_SESSION['isAuthenticated']) && $_SESSION['isAuthenticated'] && isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    switch ($role) {
        case 'admin':
        case 'manager':
            header('Location: /admin.php');
            exit;
        case 'staff':
            header('Location: /staff.php');
            exit;
        case 'sales':
            header('Location: /sales.php');
            exit;
        case 'procurement':
            header('Location: /procurement.php');
            exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $error = '';
    $isLoading = true;
    
    // Simple validation
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        // Check against demo accounts
        if (isset($demoAccounts[$username]) && $demoAccounts[$username] === $password) {
            // Login successful - in a real app, you'd verify against a database
            $_SESSION['isAuthenticated'] = true;
            $_SESSION['user'] = [
                'username' => $username,
                'role' => $username // For demo, role is same as username
            ];
            
            // Log the values to console (will show in HTML)
            echo "<script>console.log('Login successful:', { username: '$username', password: '$password' });</script>";
            
            // Redirect based on role
            switch ($username) {
                case 'admin':
                case 'manager':
                    header('Location: /JO-TECH-STOCK-MANAGEMENT/AdminDashboard.php');
                    exit;
                case 'staff':
                    header('Location: /staff.php');
                    exit;
                case 'sales':
                    header('Location: /sales.php');
                    exit;
                case 'procurement':
                    header('Location: /procurement.php');
                    exit;
            }
        } else {
            $error = 'Invalid username or password';
            echo "<script>console.log('Login failed:', { username: '$username', password: '$password' });</script>";
        }
    }
    
    $isLoading = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JO TECH Stock Management System</title>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            padding: 1rem;
        }
        .login-container {
            max-width: 28rem;
            width: 100%;
            margin: 0 auto;
        }
        .input-field {
            appearance: none;
            position: relative;
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            color: #111827;
        }
        .input-field:focus {
            outline: none;
            ring: 2px;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }
        .rounded-t {
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }
        .rounded-b {
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
        .btn-primary {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.375rem;
            color: white;
            background-color: #3b82f6;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }
        .btn-primary:focus {
            outline: none;
            ring: 2px;
            ring-offset: 2px;
            ring-color: #3b82f6;
        }
        .text-error {
            color: #ef4444;
            font-size: 0.875rem;
            text-align: center;
        }
         .space-y-8 > * + * { margin-top: 2rem; }
    .space-y-6 > * + * { margin-top: 1.5rem; }
    .space-y-2 > * + * { margin-top: 0.5rem; }
    .text-center { text-align: center; }
    .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
    .font-extrabold { font-weight: 800; }
    .mt-6 { margin-top: 1.5rem; }
    .text-2xl { font-size: 1.5rem; line-height: 2rem; }
    .font-bold { font-weight: 700; }
    .mt-2 { margin-top: 0.5rem; }
    .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
    .text-gray-600 { color: #4b5563; }
    .text-gray-900 { color: #111827; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="space-y-8">
            <div>
                <h1 class="text-center text-3xl font-extrabold text-gray-900">
                    JO TECH
                </h1>
                <h2 class="mt-6 text-center text-2xl font-bold text-gray-900">
                    Stock Management System
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sign in to your account
                </p>
            </div>
            <form class="mt-8 space-y-6" method="POST" action="login.php">
                <div class="space-y-2">
                    <div>
                        <label for="username" class="sr-only">
                            Username
                        </label>
                        <input id="username" name="username" type="text" required 
                               class="input-field rounded-t" 
                               placeholder="Username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="password" class="sr-only">
                            Password
                        </label>
                        <input id="password" name="password" type="password" required 
                               class="input-field rounded-b" 
                               placeholder="Password">
                    </div>
                </div>
                <?php if ($error): ?>
                    <div class="text-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div>
                    <button type="submit" class="btn-primary" <?php echo $isLoading ? 'disabled' : ''; ?>>
                        <?php echo $isLoading ? 'Signing in...' : 'Sign in'; ?>
                    </button>
                </div>
                <div class="text-sm text-center">
                    <p class="text-gray-600">
                        Demo accounts: admin/admin123, manager/manager123, staff/staff123,
                        sales/sales123, procurement/proc123
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>