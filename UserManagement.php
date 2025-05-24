<?php
// user-management.php
$title = "User Management";
session_start();

// Check if user is logged in
if (!isset($_SESSION['isAuthenticated'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['user']['username'] ?? 'User';

// Mock data for users (in a real app, this would come from a database)
$users = [
    [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@jotech.com',
        'role' => 'admin',
        'lastLogin' => '2023-07-15 09:30 AM'
    ],
    [
        'id' => 2,
        'name' => 'Jane Smith',
        'email' => 'jane.smith@jotech.com',
        'role' => 'manager',
        'lastLogin' => '2023-07-15 08:45 AM'
    ],
    [
        'id' => 3,
        'name' => 'Mike Johnson',
        'email' => 'mike.johnson@jotech.com',
        'role' => 'staff',
        'lastLogin' => '2023-07-14 04:20 PM'
    ],
    [
        'id' => 4,
        'name' => 'Sarah Williams',
        'email' => 'sarah.williams@jotech.com',
        'role' => 'sales',
        'lastLogin' => '2023-07-14 03:15 PM'
    ],
    [
        'id' => 5,
        'name' => 'David Brown',
        'email' => 'david.brown@jotech.com',
        'role' => 'procurement',
        'lastLogin' => '2023-07-14 02:30 PM'
    ],
    [
        'id' => 6,
        'name' => 'Emily Davis',
        'email' => 'emily.davis@jotech.com',
        'role' => 'staff',
        'lastLogin' => '2023-07-13 11:45 AM'
    ],
    [
        'id' => 7,
        'name' => 'Robert Wilson',
        'email' => 'robert.wilson@jotech.com',
        'role' => 'sales',
        'lastLogin' => '2023-07-13 10:20 AM'
    ],
    [
        'id' => 8,
        'name' => 'Lisa Taylor',
        'email' => 'lisa.taylor@jotech.com',
        'role' => 'procurement',
        'lastLogin' => '2023-07-12 04:10 PM'
    ]
];

// Role options
$roles = ['admin', 'manager', 'staff', 'sales', 'procurement'];

// Handle form submissions
$isModalOpen = isset($_GET['modal']);
$modalMode = isset($_GET['mode']) ? $_GET['mode'] : 'add';
$editingUserId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editingUser = null;

if ($editingUserId) {
    foreach ($users as $user) {
        if ($user['id'] === $editingUserId) {
            $editingUser = $user;
            break;
        }
    }
}

// In a real application, you would handle form submissions here to add/edit/delete users
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - JO TECH</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            background: #f3f4f6;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
        /* Utility classes */
        .bg-white { background: #fff; }
        .bg-gray-50 { background: #f9fafb; }
        .bg-gray-100 { background: #f3f4f6; }
        .bg-gray-500 { background: #6b7280; }
        .bg-blue-100 { background: #dbeafe; }
        .bg-blue-600 { background: #2563eb; }
        .bg-blue-700 { background: #1d4ed8; }
        .bg-green-100 { background: #d1fae5; }
        .bg-purple-100 { background: #ede9fe; }
        .bg-yellow-100 { background: #fef9c3; }
        .bg-orange-100 { background: #ffedd5; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-full { border-radius: 9999px; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }
        .text-lg { font-size: 1.125rem; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .text-base { font-size: 1rem; }
        .text-white{ color: #fff; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-700 { color: #374151; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-900 { color: #111827; }
        .text-blue-500 { color: #3b82f6; }
        .text-blue-600 { color: #2563eb; }
        .text-blue-700 { color: #1d4ed8; }
        .text-purple-800 { color: #6d28d9; }
        .text-green-800 { color: #065f46; }
        .text-yellow-800 { color: #b45309; }
        .text-orange-800 { color: #c2410c; }
        .text-red-600 { color: #dc2626; }
        .text-red-900 { color: #7f1d1d; }
        .whitespace-nowrap { white-space: nowrap; }
        .overflow-x-auto { overflow-x: auto; }
        .overflow-y-auto { overflow-y: auto; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .flex-shrink-0 { flex-shrink: 0; }
        .flex-1 { flex: 1 1 0%; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .items-between { justify-content: space-between; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .inline-flex { display: inline-flex; }
        .inline-block { display: inline-block; }
        .block { display: block; }
        .w-full { width: 100%; }
        .w-auto { width: auto; }
        .w-4 { width: 1rem; }
        .w-5 { width: 1.25rem; }
        .w-6 { width: 1.5rem; }
        .w-10 { width: 2.5rem; }
        .w-12 { width: 3rem; }
        .h-4 { height: 1rem; }
        .h-5 { height: 1.25rem; }
        .h-6 { height: 1.5rem; }
        .h-10 { height: 2.5rem; }
        .h-12 { height: 3rem; }
        .min-w-full { min-width: 100%; }
        .max-w-lg { max-width: 32rem; }
        .mr-2 { margin-right: 0.5rem; }
        .mr-4 { margin-right: 1rem; }
        .ml-3 { margin-left: 0.75rem; }
        .ml-4 { margin-left: 1rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-3 { margin-top: 0.75rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .mb-0 { margin-bottom: 0; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
        .space-x-4 > :not([hidden]) ~ :not([hidden]) { margin-left: 1rem; }
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-0\.5 { padding-top: 0.125rem; padding-bottom: 0.125rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        .pt-4 { padding-top: 1rem; }
        .pt-5 { padding-top: 1.25rem; }
        .pb-4 { padding-bottom: 1rem; }
        .pb-20 { padding-bottom: 5rem; }
        .sm\:block { display: block; }
        .sm\:inline-block { display: inline-block; }
        .sm\:align-middle { vertical-align: middle; }
        .sm\:my-8 { margin-top: 2rem; margin-bottom: 2rem; }
        .sm\:ml-3 { margin-left: 0.75rem; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:w-auto { width: auto; }
        .sm\:w-full { width: 100%; }
        .sm\:p-0 { padding: 0; }
        .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .sm\:pb-4 { padding-bottom: 1rem; }
        .sm\:text-sm { font-size: 0.875rem; }
        .sm\:max-w-lg { max-width: 32rem; }
        .sm\:flex { display: flex; }
        .sm\:items-start { align-items: flex-start; }
        .sm\:ml-4 { margin-left: 1rem; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:my-8 { margin-top: 2rem; margin-bottom: 2rem; }
        .sm\:align-middle { vertical-align: middle; }
        .sm\:w-full { width: 100%; }
        .sm\:w-auto { width: auto; }
        .sm\:text-sm { font-size: 0.875rem; }
        .sm\:p-6 { padding: 1.5rem; }
        .sm\:pb-4 { padding-bottom: 1rem; }
        .sm\:flex-row-reverse { flex-direction: row-reverse; }
        .pointer-events-none { pointer-events: none; }
        .absolute { position: absolute; }
        .relative { position: relative; }
        .fixed { position: fixed; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .inset-y-0 { top: 0; bottom: 0; }
        .right-0 { right: 0; }
        .z-10 { z-index: 10; }
        .transition-opacity { transition: opacity 0.2s; }
        .transform { transform: none; }
        .inline-block { display: inline-block; }
        .align-bottom { vertical-align: bottom; }
        .overflow-hidden { overflow: hidden; }
        .shadow-xl { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -2px rgba(0,0,0,0.05); }
        .divide-y > :not([hidden]) ~ :not([hidden]) { border-top: 1px solid #e5e7eb; }
        .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: #e5e7eb; }
        .tracking-wider { letter-spacing: 0.05em; }
        .uppercase { text-transform: uppercase; }
        .leading-5 { line-height: 1.25rem; }
        .leading-6 { line-height: 1.5rem; }
        .inline-flex { display: inline-flex; }
        .justify-center { justify-content: center; }
        .border { border-width: 1px; border-style: solid; border-color: #d1d5db; }
        .border-gray-300 { border-color: #d1d5db; }
        .border-gray-400 { border-color: #9ca3af; }
        .border-transparent { border-color: transparent; }
        .focus\:outline-none:focus { outline: none; }
        .focus\:ring-1:focus { box-shadow: 0 0 0 1px #2563eb; }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px #2563eb; }
        .focus\:ring-blue-500:focus { box-shadow: 0 0 0 2px #3b82f6; }
        .focus\:border-blue-500:focus { border-color: #3b82f6; }
        .focus\:ring-offset-2:focus { box-shadow: 0 0 0 2px #fff, 0 0 0 4px #2563eb; }
        .hover\:bg-blue-700:hover { background: #1d4ed8; }
        .hover\:bg-gray-50:hover { background: #f9fafb; }
        .hover\:text-blue-900:hover { color: #1e3a8a; }
        .hover\:text-red-900:hover { color: #7f1d1d; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-6 { gap: 1.5rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .inline-flex { display: inline-flex; }
        .rounded-full { border-radius: 9999px; }
        .px-2\.5 { padding-left: 0.625rem; padding-right: 0.625rem; }
        /* Hide scrollbars for modal overlay */
        .fixed.inset-0 { overflow-y: auto; }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php 
        // Include header with the current title
        include 'header.php'; 
        ?>
        
        <main class="content-area">

<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white p-4 shadow-sm rounded-lg">
        <h2 class="text-lg font-semibold mb-4">User Management</h2>
        <p class="text-gray-600">
            Manage users and assign roles to control access to different
            features of the system.
        </p>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-sm mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex items-center mb-4 md:mb-0">
            <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h2 class="text-lg font-semibold">Users</h2>
        </div>
        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" class="block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search users..." />
            </div>
            <div class="relative inline-block text-left">
                <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role; ?>">
                            <?php echo ucfirst($role); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <a href="?modal=true&mode=add" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add User
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Last Login
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo $user['name']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $user['email']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $roleClasses = [
                                'admin' => 'bg-purple-100 text-purple-800',
                                'manager' => 'bg-blue-100 text-blue-800',
                                'staff' => 'bg-green-100 text-green-800',
                                'sales' => 'bg-yellow-100 text-yellow-800',
                                'procurement' => 'bg-orange-100 text-orange-800'
                            ];
                            $class = $roleClasses[$user['role']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $class; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $user['lastLogin']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="?modal=true&mode=edit&edit=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-4">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="?delete=<?php echo $user['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this user?')">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium">1</span> to
            <span class="font-medium"><?php echo count($users); ?></span> of
            <span class="font-medium"><?php echo count($users); ?></span> users
        </div>
    </div>
</div>

<!-- User Modal -->
<?php if ($isModalOpen): ?>
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">
                &#8203;
            </span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <?php echo $modalMode === 'add' ? 'Add New User' : 'Edit User'; ?>
                            </h3>
                            <form method="POST" action="" class="mt-4 space-y-4">
                                <div>
                                    <label for="userName" class="block text-sm font-medium text-gray-700">
                                        Full Name
                                    </label>
                                    <input type="text" name="userName" id="userName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter full name" value="<?php echo $editingUser ? $editingUser['name'] : ''; ?>">
                                </div>
                                <div>
                                    <label for="userEmail" class="block text-sm font-medium text-gray-700">
                                        Email
                                    </label>
                                    <input type="email" name="userEmail" id="userEmail" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter email address" value="<?php echo $editingUser ? $editingUser['email'] : ''; ?>">
                                </div>
                                <div>
                                    <label for="userRole" class="block text-sm font-medium text-gray-700">
                                        Role
                                    </label>
                                    <select id="userRole" name="userRole" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role; ?>" <?php echo ($editingUser && $editingUser['role'] === $role) ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($role); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if ($modalMode === 'add'): ?>
                                    <div>
                                        <label for="userPassword" class="block text-sm font-medium text-gray-700">
                                            Temporary Password
                                        </label>
                                        <input type="password" name="userPassword" id="userPassword" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter temporary password">
                                    </div>
                                <?php endif; ?>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" name="<?php echo $modalMode === 'add' ? 'addUser' : 'updateUser'; ?>" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        <?php echo $modalMode === 'add' ? 'Add' : 'Save'; ?>
                                    </button>
                                    <a href="?" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

 </main>
    </div>
</body>
</html>