<?php
session_start();

// Simulated user retrieved from DB (replace this with actual DB query)
$user = $_SESSION['user'] ?? ['role' => 'guest'];

// Define navigation items based on user role
function getNavItems($role)
{
    switch ($role) {
        case 'admin':
        case 'manager':
        case 'guest':
            return [
                ['name' => 'Dashboard', 'path' => 'admin/index.php', 'icon' => 'home'],
                ['name' => 'User Management', 'path' => 'admin/users.php', 'icon' => 'users'],
                ['name' => 'Stock Management', 'path' => 'admin/stock.php', 'icon' => 'box'],
                ['name' => 'Categories', 'path' => 'admin/categories.php', 'icon' => 'tag'],
                ['name' => 'Reports', 'path' => 'admin/reports.php', 'icon' => 'file-text'],
            ];
        case 'staff':
            return [
                ['name' => 'Dashboard', 'path' => 'staff/index.php', 'icon' => 'home'],
                ['name' => 'Stock Management', 'path' => 'staff/stock.php', 'icon' => 'box'],
            ];
        case 'sales':
            return [
                ['name' => 'Dashboard', 'path' => 'sales/index.php', 'icon' => 'home'],
                ['name' => 'View Stock', 'path' => 'sales/stock.php', 'icon' => 'box'],
            ];
        case 'procurement':
            return [
                ['name' => 'Dashboard', 'path' => 'procurement/index.php', 'icon' => 'home'],
                ['name' => 'Stock Alerts', 'path' => 'procurement/alerts.php', 'icon' => 'alert-triangle'],
                ['name' => 'View Stock', 'path' => 'procurement/stock.php', 'icon' => 'box'],
            ];
        default:
            return [];
    }
}

$navItems = getNavItems($user['role']);
$currentPath = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>JO TECH</h2>
    </div>
    <nav class="nav-menu">
        <?php foreach ($navItems as $item): ?>
            <a href="<?= $item['path'] ?>" class="nav-link <?= strpos($item['path'], $currentPath) !== false ? 'active' : '' ?>">
                <?= file_get_contents(__DIR__ . "/icons/{$item['icon']}.svg") ?>
                <span><?= $item['name'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
