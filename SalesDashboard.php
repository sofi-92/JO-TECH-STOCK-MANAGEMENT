<?php
// sales.php
$title = "Sales Dashboard";
ob_start();
?>

<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white p-4 shadow-sm rounded-lg">
        <h2 class="text-lg font-semibold mb-4">
            Welcome to the Sales Dashboard
        </h2>
        <p class="text-gray-600">
            As a sales team member, you can view current stock levels to inform
            customers about product availability.
        </p>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-sm mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 class="text-lg font-semibold mb-4 md:mb-0">
            Product Inventory
        </h2>
        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <?= getIconSvg('search', 'h-5 w-5 text-gray-400') ?>
                </div>
                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search products...">
            </div>
            <div class="relative inline-block text-left">
                <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <?php
                    $categories = ['All Categories', 'Stationery', 'Computers', 'Accessories'];
                    foreach ($categories as $category): ?>
                    <option value="<?= $category === 'All Categories' ? '' : $category ?>">
                        <?= $category ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $products = [
                    ['id' => 1, 'name' => 'HP Printer Ink (Black)', 'category' => 'Accessories', 'stock' => 5, 'price' => '$45.99'],
                    ['id' => 2, 'name' => 'A4 Paper Reams', 'category' => 'Stationery', 'stock' => 12, 'price' => '$4.99'],
                    ['id' => 3, 'name' => 'Dell Latitude Laptop', 'category' => 'Computers', 'stock' => 8, 'price' => '$1,299.99'],
                    ['id' => 4, 'name' => 'Wireless Mouse', 'category' => 'Accessories', 'stock' => 23, 'price' => '$24.99'],
                    ['id' => 5, 'name' => 'USB-C Cables', 'category' => 'Accessories', 'stock' => 42, 'price' => '$12.99'],
                    ['id' => 6, 'name' => 'Stapler', 'category' => 'Stationery', 'stock' => 18, 'price' => '$8.99'],
                    ['id' => 7, 'name' => 'Ballpoint Pens (Blue)', 'category' => 'Stationery', 'stock' => 145, 'price' => '$0.99'],
                    ['id' => 8, 'name' => 'Wireless Keyboard', 'category' => 'Accessories', 'stock' => 15, 'price' => '$49.99']
                ];
                
                foreach ($products as $product): 
                    $statusClass = $product['stock'] > 10 ? 'bg-green-100 text-green-800' : 
                                  ($product['stock'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                    $statusText = $product['stock'] > 10 ? 'In Stock' : 
                                 ($product['stock'] > 0 ? 'Low Stock' : 'Out of Stock');
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $product['name'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['category'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['stock'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['price'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                            <?= $statusText ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">24</span> results
        </div>
        <div class="flex space-x-2">
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Previous
            </button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Popular Categories Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="font-semibold text-gray-800 mb-4">
            Popular Categories
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Stationery</span>
                <div class="w-2/3 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Computers</span>
                <div class="w-2/3 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 45%"></div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Accessories</span>
                <div class="w-2/3 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Availability Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="font-semibold text-gray-800 mb-4">
            Stock Availability
        </h3>
        <div class="flex items-center justify-around">
            <div class="text-center">
                <div class="inline-flex items-center justify-center p-4 bg-green-100 rounded-full">
                    <span class="text-xl font-bold text-green-800">75%</span>
                </div>
                <p class="mt-2 text-sm text-gray-500">In Stock</p>
            </div>
            <div class="text-center">
                <div class="inline-flex items-center justify-center p-4 bg-yellow-100 rounded-full">
                    <span class="text-xl font-bold text-yellow-800">20%</span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Low Stock</p>
            </div>
            <div class="text-center">
                <div class="inline-flex items-center justify-center p-4 bg-red-100 rounded-full">
                    <span class="text-xl font-bold text-red-800">5%</span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Out of Stock</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="space-y-4">
            <button class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <span>Check product availability</span>
                <?= getIconSvg('search', 'h-4 w-4') ?>
            </button>
            <button class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <span>Filter by category</span>
                <?= getIconSvg('filter', 'h-4 w-4') ?>
            </button>
            <button class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <span>Contact warehouse</span>
                <?= getIconSvg('box', 'h-4 w-4') ?>
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
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        'filter' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>',
        'box' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>