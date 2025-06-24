<?php
// sidebar.php

// Check if user is logged in
$userRole = $_SESSION['role'] ?? '';
$currentPath = $_SERVER['PHP_SELF'];

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
        case 'store':
            return [
                ['name' => 'Dashboard', 'path' => 'StoreDashboard.php', 'icon' => 'home'],
                ['name' => 'Stock Management', 'path' => '/JO-TECH-STOCK-MANAGEMENT/StockManagement.php', 'icon' => 'box'],
                ['name' => 'Categories', 'path' => '/JO-TECH-STOCK-MANAGEMENT/ProductCategories.php', 'icon' => 'tag']
            ];
        case 'sales':
            return [
                ['name' => 'Dashboard', 'path' => 'sales.php', 'icon' => 'home'],
            
            ];
        case 'procurement':
            return [
                ['name' => 'Dashboard', 'path' => 'ProcurementDashboard.php', 'icon' => 'home'],
                ['name' => 'Stock Alerts', 'path' => 'stock_alerts.php', 'icon' => 'alert-triangle'],
                ['name' => 'View Stock', 'path' => 'view_stock.php', 'icon' => 'box']
            ];
        default:
            return [];
    }
}

$navItems = getNavItems($userRole);

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: login.php');
    exit;
}
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="images/Jo_Tech Logo.jpg" alt="JO TECH Logo" class="logo">
        </div>
        <h2 class="company-name">JO TECH</h2>
        <button class="toggle-btn" id="toggleSidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" class="logo" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12h18M3 6h18M3 18h18"></path>
            </svg>
        </button>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-items">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo $item['path']; ?>" class="nav-item <?php echo basename($currentPath) === basename($item['path']) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <?php echo getIconSvg2($item['icon']); ?>
                    </span>
                    <span class="nav-text"><?php echo $item['name']; ?></span>
                    <span class="active-indicator"></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="avatar">
                <?php echo getIconSvg2('user'); ?>
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo $_SESSION['user']['name'] ?? 'User'; ?></span>
                <span class="user-role"><?php echo ucfirst($userRole); ?></span>
            </div>
        </div>
        <a href="logout.php" class="logout-btn">
            <span class="nav-icon">
                <?php echo getIconSvg2('logout'); ?>
            </span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
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
        case 'user':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
        case 'logout':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>';
        default:
            return '';
    }
}
?>

<style>
:root {
    --sidebar-bg: #1a365d;
    --sidebar-text: #e2e8f0;
    --sidebar-hover: #2c5282;
    --sidebar-active: #3182ce;
    --sidebar-border: #2d3748;
    --sidebar-indicator: #4299e1;
    --sidebar-header: #1e429f;
    --sidebar-collapsed-width: 80px;
    --sidebar-expanded-width: 260px;
}

.sidebar {
    background-color: var(--sidebar-bg);
    color: var(--sidebar-text);
    width: var(--sidebar-expanded-width);
    height: 100vh;
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 50;
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow-y: auto;
}

.sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
}

.sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid var(--sidebar-border);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    position: relative;
}

.logo-container {
    width: 85px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: white;
    border-radius: 50%;
    
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}
.logo{
    border-radius:50%;
}

.sidebar.collapsed .logo-container {
    width: 60px;
}

.logo {
    width: 100%;
    height: auto;
    object-fit: contain;
}

.company-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: white;
    margin: 0;
    transition: opacity 0.3s ease;
}

.sidebar.collapsed .company-name {
    opacity: 0;
    width: 0;
    height: 0;
    overflow: hidden;
}

.toggle-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    color: var(--sidebar-text);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-btn:hover {
    background-color: var(--sidebar-hover);
    transform: rotate(90deg);
}

.sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    overflow-y: auto;
}

.nav-items {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 0 0.75rem;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    font-weight: 500;
    border-radius: 0.375rem;
    color: var(--sidebar-text);
    text-decoration: none;
    position: relative;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
}

.sidebar.collapsed .nav-item {
    padding: 0.75rem;
    justify-content: center;
}

.nav-item:hover {
    background-color: var(--sidebar-hover);
    color: white;
    transform: translateX(4px);
}

.sidebar.collapsed .nav-item:hover {
    transform: none;
}

.nav-item.active {
    background-color: var(--sidebar-active);
    color: white;
    font-weight: 600;
}

.nav-item.active .nav-icon {
    color: white;
}

.nav-icon {
    margin-right: 0.75rem;
    display: flex;
    align-items: center;
    color: var(--sidebar-text);
    transition: color 0.2s ease;
    flex-shrink: 0;
}

.sidebar.collapsed .nav-icon {
    margin-right: 0;
}

.nav-text {
    flex: 1;
    transition: opacity 0.3s ease;
}

.sidebar.collapsed .nav-text {
    opacity: 0;
    width: 0;
    height: 0;
    overflow: hidden;
}

.active-indicator {
    position: absolute;
    left: -8px;
    height: 60%;
    width: 4px;
    background-color: var(--sidebar-indicator);
    border-radius: 0 4px 4px 0;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.nav-item.active .active-indicator {
    opacity: 1;
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid var(--sidebar-border);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.sidebar.collapsed .user-profile {
    justify-content: center;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--sidebar-active);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.user-info {
    display: flex;
    flex-direction: column;
    transition: opacity 0.3s ease;
    overflow: hidden;
}

.sidebar.collapsed .user-info {
    opacity: 0;
    width: 0;
    height: 0;
}

.user-name {
    font-weight: 500;
    font-size: 0.9rem;
}

.user-role {
    font-size: 0.75rem;
    color: #a0aec0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.logout-btn {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    font-weight: 500;
    border-radius: 0.375rem;
    color: var(--sidebar-text);
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
    background-color: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.sidebar.collapsed .logout-btn {
    padding: 0.75rem;
    justify-content: center;
}

.logout-btn:hover {
    background-color: rgba(239, 68, 68, 0.3);
    color: white;
}

.logout-btn .nav-icon {
    color: #ef4444;
}

.logout-btn:hover .nav-icon {
    color: white;
}

/* Scrollbar styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: var(--sidebar-bg);
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: var(--sidebar-hover);
    border-radius: 3px;
}

/* Add smooth transition for main content when sidebar collapses */
.main-content {
    margin-left: var(--sidebar-expanded-width);
    transition: margin-left 0.3s ease;
}

.sidebar.collapsed ~ .main-content {
    margin-left: var(--sidebar-collapsed-width);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    
    // Check localStorage for saved state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
    
    // Toggle sidebar
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Adjust main content margin
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.style.marginLeft = isCollapsed ? 
            getComputedStyle(document.documentElement).getPropertyValue('--sidebar-collapsed-width') : 
            getComputedStyle(document.documentElement).getPropertyValue('--sidebar-expanded-width');
    }

    // Confirm logout
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    }
});
</script>