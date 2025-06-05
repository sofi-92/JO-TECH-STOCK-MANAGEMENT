<?php
session_start();
require_once 'config.php';
$title = "User Management";

// Check authentication and admin role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Permission denied');
}

// Initialize messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
    if (isset($_POST['addUser'])) {
    // Add new user
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        $_SESSION['error'] = "A user with this email already exists!";
    } else {
        // Email doesn't exist, proceed with adding user
        $stmt = $conn->prepare("INSERT INTO users (user_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "User added successfully!";
        } else {
            $_SESSION['error'] = "Error adding user: " . $conn->error;
        }
    }
    
    // Close statements
    $checkStmt->close();
    if (isset($stmt)) {
        $stmt->close();
    }
} elseif (isset($_POST['updateUser'])) {
            // Update existing user
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
              $phone = trim($_POST['phone']);
            $role = $_POST['role'];

      $stmt = $conn->prepare("UPDATE users SET user_name = ?, email = ?, phone = ?, role = ? WHERE user_id = ?");
$stmt->bind_param("ssssi", $name, $email, $phone, $role, $id);
            $stmt->execute();
            
            $_SESSION['success'] = "User updated successfully!";
        }
        
        header("Location:UserManagement.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location:UserManagement.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    // Delete user
    try {
        $id = (int)$_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $_SESSION['success'] = "User deleted successfully!";
        header("Location:UserManagement.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
        header("Location:UserManagement.php");
        exit;
    }
}

// Fetch users from database
$users = [];
try {
    $stmt = $conn->prepare("SELECT user_id, user_name, email, role, created_at FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Error loading users: " . $e->getMessage();
}

// Get user for editing
$editingUser = null;
if (isset($_GET['edit'])) {
    try {
        $id = (int)$_GET['edit'];
        $stmt = $conn->prepare("SELECT user_id, user_name, email, phone, role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editingUser = $result->fetch_assoc();
    } catch (Exception $e) {
        $error = "Error loading user: " . $e->getMessage();
    }
}

// Role options
$roles = ['admin', 'manager', 'store', 'sales', 'procurement'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - JO TECH</title>
    <style>
        :root {
            --primary: #3b82f6;
            --primary-light: #93c5fd;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
        }
     body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #334155;
            line-height: 1.5;
        }
      .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            
        }
        .mt-3{
            margin-top: 1rem;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
        }

   


        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1.25rem;
            text-align: left;
        }

        td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-admin {
            background-color: #f3e8ff;
            color: #9333ea;
        }

        .badge-manager {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .badge-staff {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-sales {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .badge-procurement {
            background-color: #ffedd5;
            color: #9a3412;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            border: 1px solid #e2e8f0;
            background-color: white;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
        }

        /* Alerts */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 3px solid transparent;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #059669;
            border-left-color: #059669;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border-left-color: #dc2626;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 1rem;
        }

        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            width: 100%;
            max-width: 28rem;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        /* Icons */
        .icon {
            width: 1rem;
            height: 1rem;
            stroke-width: 2;
        }

        /* Utility classes */
        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mr-4 {
            margin-right: 1rem;
        }

        .text-xl {
            font-size: 1.25rem;
        }

        .text-2xl {
            font-size: 1.5rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .hover\:text-blue-900:hover {
            color: #1e40af;
        }

        .hover\:text-red-900:hover {
            color: #991b1b;
        }

        .hidden {
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.8125rem;
            }
            
            .card-header, .card-body {
                padding: 1rem;
            }
            
            td, th {
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
             <main class="content-area">

        <!-- Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><?= $success ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <div class="card mb-6">
            <div class="card-header flex justify-between items-center">
                <h2 class="text-xl font-semibold">User Management</h2>
                <a href="?modal=add" class="btn btn-primary">
                    <svg class="icon mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add User
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['user_name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $user['role'] ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <div class="flex items-center">
                                            <a href="?edit=<?= $user['user_id'] ?>" class="text-blue-600 hover:text-blue-900 mr-4">
                                                Edit
                                            </a>
                                            <a href="?delete=<?= $user['user_id'] ?>" class="text-red-600 hover:text-red-900" 
                                               onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
     <!-- Modal -->
<?php if (isset($_GET['edit']) || isset($_GET['modal'])): ?>
    <div class="modal-overlay">
        <div class="modal-content">
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-4">
                    <?= isset($_GET['edit']) ? 'Edit User' : 'Add New User' ?>
                </h3>

                <form method="POST" onsubmit="return validatePasswords()">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="id" value="<?= $editingUser['user_id'] ?>">
                    <?php endif; ?>

                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="<?= $editingUser['user_name'] ?? '' ?>" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="<?= $editingUser['email'] ?? '' ?>" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="<?= $editingUser['phone'] ?? '' ?>" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role ?>" <?= ($editingUser['role'] ?? '') === $role ? 'selected' : '' ?>>
                                        <?= ucfirst($role) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!isset($_GET['edit'])): ?>
        <div class="form-group">
    <label class="form-label">Password</label>
    <div class="relative">
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-500 hover:text-gray-700" onclick="togglePasswordVisibility('password', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </span>
    </div>
</div>

<div class="form-group">
    <label class="form-label">Confirm Password</label>
    <div class="relative">
        <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Confirm password" required>
        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-500 hover:text-gray-700" onclick="togglePasswordVisibility('confirmPassword', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </span>
    </div>
</div>
                        <?php endif; ?>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" onclick="window.location.href='usermanagement.php'" 
                                    class="btn btn-outline">
                                Cancel
                            </button>
                            <button type="submit" name="<?= isset($_GET['edit']) ? 'updateUser' : 'addUser' ?>" 
                                    class="btn btn-primary">
                                <?= isset($_GET['edit']) ? 'Save Changes' : 'Add User' ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
        </main>
    </div>
</body>
</html>


<script>
function validatePasswords() {
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert("Passwords do not match. Please try again.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}
function togglePasswordVisibility(fieldId, iconElement) {
    const passwordField = document.getElementById(fieldId);
    const icon = iconElement.querySelector('svg');
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        `;
    } else {
        passwordField.type = "password";
        icon.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        `;
    }
}
</script>