<?php
// layout.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['isAuthenticated'])) {
    header('Location: login.php');
    exit;
}

$title = $title ?? 'Dashboard'; // Default title if not set
$username = $_SESSION['user']['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - JO TECH</title>
    <style>
        .dashboard-container {
            display: flex;
            height: 100vh;
            background-color: #f3f4f6;
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
            <?php 
            // This is where the content of individual pages will be included
            if (isset($content)) {
                include $content;
            } 
            ?>
        </main>
    </div>
</body>
</html>