<?php
// procurement.php
$title = "Procurement Dashboard";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - JO TECH</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary: #10b981;
            --danger: #dc2626;
            --warning: #f59e0b;
            --success: #10b981;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--gray-100);
            color: var(--gray-900);
            line-height: 1.5;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            background: var(--gray-100);
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
        
        /* Background Colors */
        .bg-white { background: #fff; }
        .bg-gray-50 { background: var(--gray-50); }
        .bg-gray-100 { background: var(--gray-100); }
        .bg-gray-200 { background: var(--gray-200); }
        .bg-primary { background: var(--primary); }
        .bg-primary-dark { background: var(--primary-dark); }
        .bg-primary-light { background: var(--primary-light); }
        .bg-danger-light { background: #fee2e2; }
        .bg-warning-light { background: #fef3c7; }
        .bg-success-light { background: #d1fae5; }
        
        /* Text Colors */
        .text-primary { color: var(--primary); }
        .text-primary-dark { color: var(--primary-dark); }
        .text-secondary { color: var(--secondary); }
        .text-danger { color: var(--danger); }
        .text-warning { color: var(--warning); }
        .text-success { color: var(--success); }
        .text-gray-400 { color: var(--gray-400); }
        .text-gray-500 { color: var(--gray-500); }
        .text-gray-600 { color: var(--gray-600); }
        .text-gray-700 { color: var(--gray-700); }
        .text-gray-800 { color: var(--gray-800); }
        .text-gray-900 { color: var(--gray-900); }
        
        /* Spacing */
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .m-0 { margin: 0; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .mr-1 { margin-right: 0.25rem; }
        .mr-2 { margin-right: 0.5rem; }
        .mr-3 { margin-right: 0.75rem; }
        .ml-auto { margin-left: auto; }
        
        /* Sizing */
        .w-full { width: 100%; }
        .w-auto { width: auto; }
        .h-4 { height: 1rem; }
        .h-5 { height: 1.25rem; }
        .h-6 { height: 1.5rem; }
        .h-8 { height: 2rem; }
        
        /* Typography */
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-3xl { font-size: 1.875rem; }
        .font-normal { font-weight: 400; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .leading-tight { line-height: 1.25; }
        .leading-snug { line-height: 1.375; }
        
        /* Flex & Grid */
        .flex { display: flex; }
        .inline-flex { display: inline-flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .flex-wrap { flex-wrap: wrap; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .items-end { align-items: flex-end; }
        .justify-start { justify-content: flex-start; }
        .justify-end { justify-content: flex-end; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .justify-around { justify-content: space-around; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        
        /* Borders */
        .border { border-width: 1px; border-style: solid; }
        .border-0 { border-width: 0; }
        .border-gray-200 { border-color: var(--gray-200); }
        .border-gray-300 { border-color: var(--gray-300); }
        .border-primary { border-color: var(--primary); }
        .border-transparent { border-color: transparent; }
        .rounded { border-radius: 0.25rem; }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-full { border-radius: 9999px; }
        
        /* Shadows */
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        
        /* Effects */
        .transition { transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .duration-200 { transition-duration: 200ms; }
        
        /* Interactive Elements */
        .cursor-pointer { cursor: pointer; }
        .hover\:bg-primary-dark:hover { background-color: var(--primary-dark); }
        .hover\:shadow-lg:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .focus\:outline-none:focus { outline: none; }
        .focus\:ring:focus { box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.5); }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.5); }
        .focus\:ring-offset-2:focus { box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(37, 99, 235, 0.5); }
        
        /* Tables */
        .table-auto { table-layout: auto; }
        .table-fixed { table-layout: fixed; }
        .border-collapse { border-collapse: collapse; }
        
        /* Utilities */
        .overflow-hidden { overflow: hidden; }
        .overflow-x-auto { overflow-x: auto; }
        .overflow-y-auto { overflow-y: auto; }
        .whitespace-nowrap { white-space: nowrap; }
        .min-w-full { min-width: 100%; }
        .divide-y > :not([hidden]) ~ :not([hidden]) { border-top-width: 1px; border-top-color: var(--gray-200); }
        
        /* Custom Components */
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
        }
        
        .btn-outline:hover {
            background-color: var(--gray-50);
            border-color: var(--gray-400);
        }
        
        .progress-bar {
            height: 0.5rem;
            border-radius: 0.25rem;
            background-color: var(--gray-200);
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            border-radius: 0.25rem;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }
        
        /* Responsive Grid */
        @media (min-width: 640px) {
            .sm\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        
        @media (min-width: 768px) {
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .md\:flex-row { flex-direction: row; }
        }
        
        @media (min-width: 1024px) {
            .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        
        /* Custom select styling */
        select {
            appearance: none;
            background: #fff url('data:image/svg+xml;utf8,<svg fill="none" stroke="gray" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>') no-repeat right 0.75rem center/1rem 1rem;
            padding: 0.5rem 2.25rem 0.5rem 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            color: var(--gray-700);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        /* Hide scrollbars for Chrome, Safari and Opera */
        .content-area::-webkit-scrollbar {
            display: none;
        }
        
        /* Hide scrollbars for IE, Edge and Firefox */
        .content-area {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        
        /* Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .gap-6 {
            gap: 1.5rem;
        }
        .md-justify-between {
            justify-content: space-between;
        }
        .sm-flex-row{
            flex-direction: row;
        }
        @media (min-width: 768px) {
            .sm-flex-row {
            flex-direction: row;
            }
        }
        .h-fit{
            height: fit-content;
        }
        .gap-3 {
            gap: 0.75rem;
        }
    
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="card p-6">
                    <h2 class="text-xl font-semibold mb-2">
                        Welcome to the Procurement Dashboard
                    </h2>
                    <p class="text-gray-600">
                        Monitor low stock alerts and current inventory levels to ensure
                        timely procurement of supplies.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Stats Cards -->
                <div class="card p-6 hover:shadow-lg transition duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Low Stock Items</h3>
                        <?= getIconSvg('alert-triangle', 'h-6 w-6 text-danger') ?>
                    </div>
                    <p class="text-2xl font-semibold mb-1">8</p>
                    <div class="flex items-center text-sm text-danger">
                        <?= getIconSvg('trending-up', 'h-4 w-4 mr-1') ?>
                        <span>2 more than last week</span>
                    </div>
                </div>

                <div class="card p-6 hover:shadow-lg transition duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Pending Orders</h3>
                        <?= getIconSvg('shopping-cart', 'h-6 w-6 text-warning') ?>
                    </div>
                    <p class="text-2xl font-semibold mb-1">3</p>
                    <div class="flex items-center text-sm text-success">
                        <?= getIconSvg('trending-down', 'h-4 w-4 mr-1') ?>
                        <span>1 less than last week</span>
                    </div>
                </div>

                <div class="card p-6 hover:shadow-lg transition duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Products</h3>
                        <?= getIconSvg('box', 'h-6 w-6 text-primary') ?>
                    </div>
                    <p class="text-2xl font-semibold mb-1">1,284</p>
                    <div class="text-sm text-gray-500">
                        <span>Across 15 categories</span>
                    </div>
                </div>

                <div class="card p-6 hover:shadow-lg transition duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Active Suppliers</h3>
                        <?= getIconSvg('users', 'h-6 w-6 text-success') ?>
                    </div>
                    <p class="text-2xl font-semibold mb-1">24</p>
                    <div class="flex items-center text-sm text-success">
                        <?= getIconSvg('trending-up', 'h-4 w-4 mr-1') ?>
                        <span>2 new this month</span>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts Table -->
            <div class="card p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md-justify-between mb-6">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="bg-danger-light p-2 rounded-lg mr-3">
                            <?= getIconSvg('alert-triangle', 'h-5 w-5 text-danger') ?>
                        </div>
                        <h2 class="text-xl font-semibold">Low Stock Alerts</h2>
                    </div>
                    <div class="flex flex-col sm-flex-row gap-3">
                        <div class="relative">
                            <select class="w-full sm:w-auto">
                                <option value="">All Categories</option>
                                <option value="Stationery">Stationery</option>
                                <option value="Computers">Computers</option>
                                <option value="Accessories">Accessories</option>
                            </select>
                        </div>
                        <button class="btn btn-primary h-fit">
                            <?= getIconSvg('filter', 'h-4 w-4 mr-2') ?>
                            Filter
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Required</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $lowStockItems = [
                                ['id' => 1, 'name' => 'HP Printer Ink (Black)', 'category' => 'Accessories', 'currentStock' => 5, 'minRequired' => 10, 'lastOrder' => '2023-06-15', 'supplier' => 'Tech Supplies Inc.'],
                                ['id' => 2, 'name' => 'A4 Paper Reams', 'category' => 'Stationery', 'currentStock' => 12, 'minRequired' => 20, 'lastOrder' => '2023-06-28', 'supplier' => 'Paper World'],
                                ['id' => 3, 'name' => 'Dell Laptop Chargers', 'category' => 'Computers', 'currentStock' => 3, 'minRequired' => 5, 'lastOrder' => '2023-05-12', 'supplier' => 'Dell Direct'],
                                ['id' => 4, 'name' => 'Wireless Mice', 'category' => 'Accessories', 'currentStock' => 7, 'minRequired' => 15, 'lastOrder' => '2023-07-01', 'supplier' => 'Tech Supplies Inc.'],
                                ['id' => 5, 'name' => 'Stapler Pins', 'category' => 'Stationery', 'currentStock' => 8, 'minRequired' => 15, 'lastOrder' => '2023-06-10', 'supplier' => 'Office Solutions'],
                                ['id' => 6, 'name' => 'USB Flash Drives', 'category' => 'Accessories', 'currentStock' => 9, 'minRequired' => 20, 'lastOrder' => '2023-05-22', 'supplier' => 'Digital Storage Co.'],
                                ['id' => 7, 'name' => 'Highlighters', 'category' => 'Stationery', 'currentStock' => 14, 'minRequired' => 25, 'lastOrder' => '2023-06-05', 'supplier' => 'Office Solutions'],
                                ['id' => 8, 'name' => 'HDMI Cables', 'category' => 'Accessories', 'currentStock' => 6, 'minRequired' => 12, 'lastOrder' => '2023-07-03', 'supplier' => 'Tech Supplies Inc.']
                            ];
                            
                            foreach ($lowStockItems as $item): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $item['name'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['category'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-danger"><?= $item['currentStock'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['minRequired'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['lastOrder'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['supplier'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button class="text-primary hover:text-primary-dark font-medium">Order</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders Table -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="bg-primary-light p-2 rounded-lg mr-3">
                                <?= getIconSvg('shopping-cart', 'h-5 w-5 text-primary') ?>
                            </div>
                            <h2 class="text-xl font-semibold">Recent Orders</h2>
                        </div>
                        <a href="#" class="text-sm text-primary font-medium">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $recentOrders = [
                                    ['id' => 1, 'product' => 'HP Printer Paper', 'quantity' => 50, 'date' => '2023-07-14', 'status' => 'Delivered', 'supplier' => 'Paper World'],
                                    ['id' => 2, 'product' => 'USB-C Cables', 'quantity' => 30, 'date' => '2023-07-12', 'status' => 'In Transit', 'supplier' => 'Tech Supplies Inc.'],
                                    ['id' => 3, 'product' => 'Wireless Keyboards', 'quantity' => 15, 'date' => '2023-07-10', 'status' => 'Processing', 'supplier' => 'Tech Supplies Inc.'],
                                    ['id' => 4, 'product' => 'Toner Cartridges', 'quantity' => 10, 'date' => '2023-07-08', 'status' => 'Delivered', 'supplier' => 'Print Solutions']
                                ];
                                
                                foreach ($recentOrders as $order): 
                                    $statusClass = $order['status'] === 'Delivered' ? 'bg-success-light text-success' : 
                                                    ($order['status'] === 'In Transit' ? 'bg-primary-light text-primary' : 'bg-warning-light text-warning');
                                ?>
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $order['product'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $order['quantity'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $order['date'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="badge <?= $statusClass ?>">
                                            <?= $order['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Stock by Category -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="bg-primary-light p-2 rounded-lg mr-3">
                                <?= getIconSvg('box', 'h-5 w-5 text-primary') ?>
                            </div>
                            <h2 class="text-xl font-semibold">Stock by Category</h2>
                        </div>
                        <a href="#" class="text-sm text-primary font-medium">View Details</a>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Stationery</span>
                                <span class="text-sm font-medium text-gray-900">543 items (42%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: 42%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Computers</span>
                                <span class="text-sm font-medium text-gray-900">328 items (26%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: 26%; background-color: #10b981;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Accessories</span>
                                <span class="text-sm font-medium text-gray-900">413 items (32%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: 32%; background-color: #f59e0b;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button class="btn btn-primary w-full">
                            View Full Inventory Report
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php

function getIconSvg($iconName, $classes = '') {
    $icons = [
        'alert-triangle' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        'trending-up' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>',
        'trending-down' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>',
        'filter' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>',
        'shopping-cart' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
        'box' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 16.96 3.27 8.04 12 12"></polyline><polyline points="12 12 20.73 8.04 20.73 16.96"></polyline><line x1="12" y1="22" x2="12" y2="12"></line></svg>',
                'users' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'chevron-down' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>',
        'plus' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'refresh' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        'download' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>',
        'printer' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>',
        'file-text' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>

<script>
// Simple JavaScript for interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add click event to all order buttons in the low stock table
    document.querySelectorAll('[data-order-btn]').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            alert(`Order initiated for ${productName} (ID: ${productId})`);
            // In a real application, this would open a modal or redirect to order page
        });
    });
    
    // Refresh button functionality
    const refreshBtn = document.getElementById('refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.classList.add('animate-spin');
            setTimeout(() => {
                this.classList.remove('animate-spin');
                // In a real app, this would refresh the data
                alert('Data refreshed!');
            }, 1000);
        });
    }
    
    // Export buttons functionality
    document.querySelectorAll('[data-export]').forEach(button => {
        button.addEventListener('click', function() {
            const exportType = this.getAttribute('data-export');
            alert(`Exporting data as ${exportType.toUpperCase()}`);
            // In a real app, this would trigger a file download
        });
    });
});
</script>