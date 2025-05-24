<?php
// sidebar.php


// Check if user is logged in


$userRole = $_SESSION['user']['role'] ?? '';
$currentPath = $_SERVER['PHP_SELF']; // Gets current PHP file path

// Define navigation items based on user role
function getNavItems($role) {
    switch ($role) {
        case 'admin':
        case 'manager':
            return [
                ['name' => 'Dashboard', 'path' => '/JO-TECH-STOCK-MANAGEMENT/AdminDashboard.php', 'icon' => 'home'],
                ['name' => 'User Management', 'path' => '/JO-TECH-STOCK-MANAGEMENT/UserManagement.php', 'icon' => 'users'],
                ['name' => 'Stock Management', 'path' => '/JO-TECH-STOCK-MANAGEMENT/StockManagement.php', 'icon' => 'box'],
                ['name' => 'Categories', 'path' => '/JO-TECH-STOCK-MANAGEMENT/ProductCategories.php', 'icon' => 'tag'],
                ['name' => 'Reports', 'path' => '/JO-TECH-STOCK-MANAGEMENT/Reports.php', 'icon' => 'file-text']
            ];
        case 'staff':
            return [
                ['name' => 'Dashboard', 'path' => 'staff.php', 'icon' => 'home'],
                ['name' => 'Stock Management', 'path' => 'staff_stock.php', 'icon' => 'box']
            ];
        case 'sales':
            return [
                ['name' => 'Dashboard', 'path' => 'sales.php', 'icon' => 'home'],
                ['name' => 'View Stock', 'path' => 'sales_stock.php', 'icon' => 'box']
            ];
        case 'procurement':
            return [
                ['name' => 'Dashboard', 'path' => 'procurement.php', 'icon' => 'home'],
                ['name' => 'Stock Alerts', 'path' => 'procurement_alerts.php', 'icon' => 'alert-triangle'],
                ['name' => 'View Stock', 'path' => 'procurement_stock.php', 'icon' => 'box']
            ];
        default:
            return [];
    }
}

$navItems = getNavItems($userRole);
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>JO TECH</h2>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-items">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo $item['path']; ?>" class="nav-item <?php echo basename($currentPath) === $item['path'] ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <?php echo getIconSvg2($item['icon']); ?>
                    </span>
                    <span class="nav-text"><?php echo $item['name']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
</aside>

<?php
// Helper function to generate SVG icons
function getIconSvg2($iconName) {
    switch ($iconName) {
        case 'home':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
        case 'users':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
        case 'box':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
        case 'tag':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>';
        case 'file-text':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>';
        case 'alert-triangle':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        default:
            return '';
    }
}
?>

<style>
.sidebar {
    background-color: #1f2937;
    color: white;
    width: 16rem;
    display: none;
    flex-shrink: 0;
}

@media (min-width: 768px) {
    .sidebar {
        display: block;
    }
}

.sidebar-header {
    height: 4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #374151;
}

.sidebar-header h2 {
    font-size: 1.25rem;
    font-weight: bold;
}

.sidebar-nav {
    margin-top: 1.25rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

.nav-items {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.375rem;
    color: #d1d5db;
    text-decoration: none;
}

.nav-item:hover {
    background-color: #374151;
    color: white;
}

.nav-item.active {
    background-color: #111827;
    color: white;
}

.nav-icon {
    margin-right: 0.75rem;
    display: flex;
    align-items: center;
}

.nav-text {
    margin-top: 0.125rem; /* Small adjustment for better alignment */
}
</style>