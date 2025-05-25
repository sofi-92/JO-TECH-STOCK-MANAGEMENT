<?php
// procurement.php
$title = "Procurement Dashboard";

?>

<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white p-4 shadow-sm rounded-lg">
        <h2 class="text-lg font-semibold mb-4">
            Welcome to the Procurement Dashboard
        </h2>
        <p class="text-gray-600">
            Monitor low stock alerts and current inventory levels to ensure
            timely procurement of supplies.
        </p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Stats Cards -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Low Stock Items</h3>
            <?= getIconSvg('alert-triangle', 'h-6 w-6 text-red-600') ?>
        </div>
        <p class="text-2xl font-semibold">8</p>
        <div class="flex items-center mt-2 text-sm text-red-500">
            <?= getIconSvg('trending-up', 'h-4 w-4 mr-1') ?>
            <span>2 more than last week</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Pending Orders</h3>
            <?= getIconSvg('shopping-cart', 'h-6 w-6 text-yellow-600') ?>
        </div>
        <p class="text-2xl font-semibold">3</p>
        <div class="flex items-center mt-2 text-sm text-green-500">
            <?= getIconSvg('trending-up', 'h-4 w-4 mr-1') ?>
            <span>1 less than last week</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Total Products</h3>
            <?= getIconSvg('box', 'h-6 w-6 text-blue-600') ?>
        </div>
        <p class="text-2xl font-semibold">1,284</p>
        <div class="flex items-center mt-2 text-sm text-gray-500">
            <span>Across 15 categories</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Active Suppliers</h3>
            <?= getIconSvg('shopping-cart', 'h-6 w-6 text-green-600') ?>
        </div>
        <p class="text-2xl font-semibold">24</p>
        <div class="flex items-center mt-2 text-sm text-green-500">
            <?= getIconSvg('trending-up', 'h-4 w-4 mr-1') ?>
            <span>2 new this month</span>
        </div>
    </div>
</div>

<!-- Low Stock Alerts Table -->
<div class="bg-white p-6 rounded-lg shadow-sm mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex items-center mb-4 md:mb-0">
            <?= getIconSvg('alert-triangle', 'h-5 w-5 text-red-500 mr-2') ?>
            <h2 class="text-lg font-semibold">Low Stock Alerts</h2>
        </div>
        <div class="flex space-x-4">
            <div class="relative inline-block text-left">
                <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Categories</option>
                    <option value="Stationery">Stationery</option>
                    <option value="Computers">Computers</option>
                    <option value="Accessories">Accessories</option>
                </select>
            </div>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $item['name'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['category'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-medium"><?= $item['currentStock'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['minRequired'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['lastOrder'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['supplier'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button class="text-blue-600 hover:text-blue-900">Order</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders Table -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Recent Orders</h2>
            <?= getIconSvg('shopping-cart', 'h-5 w-5 text-blue-500') ?>
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
                        $statusClass = $order['status'] === 'Delivered' ? 'bg-green-100 text-green-800' : 
                                        ($order['status'] === 'In Transit' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800');
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $order['product'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $order['quantity'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $order['date'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
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
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Stock by Category</h2>
            <?= getIconSvg('box', 'h-5 w-5 text-blue-500') ?>
        </div>
        <div class="space-y-4">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-600">Stationery</span>
                    <span class="text-sm font-medium text-gray-900">543 items</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-600">Computers</span>
                    <span class="text-sm font-medium text-gray-900">328 items</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-600">Accessories</span>
                    <span class="text-sm font-medium text-gray-900">413 items</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-600">Others</span>
                    <span class="text-sm font-medium text-gray-900">124 items</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 20%"></div>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <button class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                View Full Inventory
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';

// Add these icons to your getIconSvg function if not already present
function getIconSvg($iconName, $classes = '') {
    $icons = [
        // ... previous icons ...
        'trending-up' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>',
        'filter' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>',
        'shopping-cart' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>