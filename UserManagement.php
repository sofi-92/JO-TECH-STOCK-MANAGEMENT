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
            $role = $_POST['role'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (user_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $role);
            $stmt->execute();
            
            $_SESSION['success'] = "User added successfully!";
        } elseif (isset($_POST['updateUser'])) {
            // Update existing user
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];

            $stmt = $conn->prepare("UPDATE users SET user_name = ?, email = ?, role = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $name, $email, $role, $id);
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
        $stmt = $conn->prepare("SELECT user_id, user_name, email, role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editingUser = $result->fetch_assoc();
    } catch (Exception $e) {
        $error = "Error loading user: " . $e->getMessage();
    }
}

// Role options
$roles = ['admin', 'manager', 'staff', 'sales', 'procurement'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - JO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <?php include 'header.php'; ?>

        <!-- Messages -->
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $success ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">User Management</h2>
                <a href="?modal=add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add User
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['user_name']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs <?= match($user['role']) {
                                        'admin' => 'bg-purple-100 text-purple-800',
                                        'manager' => 'bg-blue-100 text-blue-800',
                                        'staff' => 'bg-green-100 text-green-800',
                                        'sales' => 'bg-yellow-100 text-yellow-800',
                                        'procurement' => 'bg-orange-100 text-orange-800'
                                    } ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td class="px-6 py-4">
                                    <a href="?edit=<?= $user['user_id'] ?>" class="text-blue-600 hover:text-blue-900 mr-4">
                                        Edit
                                    </a>
                                    <a href="?delete=<?= $user['user_id'] ?>" class="text-red-600 hover:text-red-900" 
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <?php if (isset($_GET['edit']) || isset($_GET['modal'])): ?>
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-lg p-6 w-full max-w-md">
                    <h3 class="text-xl font-semibold mb-4">
                        <?= isset($_GET['edit']) ? 'Edit User' : 'Add New User' ?>
                    </h3>
                    
                    <form method="POST">
                        <?php if (isset($_GET['edit'])): ?>
                            <input type="hidden" name="id" value="<?= $editingUser['user_id'] ?>">
                        <?php endif; ?>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Full Name</label>
                                <input type="text" name="name" value="<?= $editingUser['user_name'] ?? '' ?>" 
                                       class="w-full p-2 border rounded" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-1">Email</label>
                                <input type="email" name="email" value="<?= $editingUser['email'] ?? '' ?>" 
                                       class="w-full p-2 border rounded" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-1">Role</label>
                                <select name="role" class="w-full p-2 border rounded" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role ?>" <?= ($editingUser['role'] ?? '') === $role ? 'selected' : '' ?>>
                                            <?= ucfirst($role) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <?php if (!isset($_GET['edit'])): ?>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Password</label>
                                    <input type="password" name="password" 
                                           class="w-full p-2 border rounded" required>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button" onclick="window.location.href='usermanagement.php'" 
                                        class="px-4 py-2 border rounded hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit" name="<?= isset($_GET['edit']) ? 'updateUser' : 'addUser' ?>" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    <?= isset($_GET['edit']) ? 'Save Changes' : 'Add User' ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>