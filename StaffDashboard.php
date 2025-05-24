<?php
// staff.php
$title = "Staff Dashboard";
ob_start();
?>

<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white p-4 shadow-sm rounded-lg">
        <h2 class="text-lg font-semibold mb-4">
            Welcome to the Warehouse Staff Dashboard
        </h2>
        <p class="text-gray-600">
            As a warehouse staff member, you can view and adjust stock levels as
            items are added, sold, or returned.
        </p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Stock Movements Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">
                Stock Movements Today
            </h3>
            <?= getIconSvg('package', 'h-6 w-6 text-blue-600') ?>
        </div>
        <p class="text-2xl font-semibold">24</p>
        <div class="flex items-center mt-2 text-sm text-green-500">
            <?= getIconSvg('arrow-up', 'h-4 w-4 mr-1') ?>
            <span>12% more than yesterday</span>
        </div>
    </div>
    
    <!-- Items Received Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">
                Items Received
            </h3>
            <?= getIconSvg('arrow-down', 'h-6 w-6 text-green-600') ?>
        </div>
        <p class="text-2xl font-semibold">15</p>
        <div class="flex items-center mt-2 text-sm text-green-500">
            <?= getIconSvg('arrow-up', 'h-4 w-4 mr-1') ?>
            <span>3 more than yesterday</span>
        </div>
    </div>
    
    <!-- Items Dispatched Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">
                Items Dispatched
            </h3>
            <?= getIconSvg('arrow-up', 'h-6 w-6 text-orange-600') ?>
        </div>
        <p class="text-2xl font-semibold">9</p>
        <div class="flex items-center mt-2 text-sm text-red-500">
            <?= getIconSvg('arrow-down', 'h-4 w-4 mr-1') ?>
            <span>2 less than yesterday</span>
        </div>
    </div>
</div>

<!-- Quick Stock Update Section -->
<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">Quick Stock Update</h2>
            <?= getIconSvg('box', 'h-5 w-5 text-blue-500') ?>
        </div>
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <?= getIconSvg('search', 'h-5 w-5 text-gray-400') ?>
                </div>
                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search for a product...">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Product 1 -->
            <div class="border border-gray-200 rounded-md p-4">
                <h3 class="font-medium mb-2">HP Printer Ink (Black)</h3>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500">Current Stock:</span>
                    <span class="font-medium">5</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="bg-gray-100 rounded-md p-1 hover:bg-gray-200">
                        <?= getIconSvg('arrow-down', 'h-4 w-4') ?>
                    </button>
                    <input type="number" class="block w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm" value="0" min="0">
                    <button class="bg-gray-100 rounded-md p-1 hover:bg-gray-200">
                        <?= getIconSvg('arrow-up', 'h-4 w-4') ?>
                    </button>
                    <button class="bg-blue-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-600">
                        Update
                    </button>
                </div>
            </div>
            
            <!-- Product 2 -->
            <div class="border border-gray-200 rounded-md p-4">
                <h3 class="font-medium mb-2">A4 Paper Reams</h3>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500">Current Stock:</span>
                    <span class="font-medium">12</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="bg-gray-100 rounded-md p-1 hover:bg-gray-200">
                        <?= getIconSvg('arrow-down', 'h-4 w-4') ?>
                    </button>
                    <input type="number" class="block w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm" value="0" min="0">
                    <button class="bg-gray-100 rounded-md p-1 hover:bg-gray-200">
                        <?= getIconSvg('arrow-up', 'h-4 w-4') ?>
                    </button>
                    <button class="bg-blue-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-600">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Stock Movements Table -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Recent Stock Movements</h2>
        <?= getIconSvg('package', 'h-5 w-5 text-blue-500') ?>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated By</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $recentMovements = [
                    ['id' => 1, 'product' => 'Dell Latitude Laptop', 'type' => 'out', 'quantity' => 2, 'date' => '2023-07-15', 'user' => 'John Doe'],
                    ['id' => 2, 'product' => 'HP Printer Paper', 'type' => 'in', 'quantity' => 50, 'date' => '2023-07-14', 'user' => 'Jane Smith'],
                    ['id' => 3, 'product' => 'Ballpoint Pens (Blue)', 'type' => 'out', 'quantity' => 25, 'date' => '2023-07-13', 'user' => 'Mike Johnson'],
                    ['id' => 4, 'product' => 'USB-C Cables', 'type' => 'in', 'quantity' => 30, 'date' => '2023-07-12', 'user' => 'Sarah Williams'],
                    ['id' => 5, 'product' => 'Wireless Keyboards', 'type' => 'out', 'quantity' => 5, 'date' => '2023-07-11', 'user' => 'David Brown']
                ];
                
                foreach ($recentMovements as $movement): 
                    $typeClass = $movement['type'] === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    $typeText = $movement['type'] === 'in' ? 'Received' : 'Dispatched';
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $movement['product'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeClass ?>">
                            <?= $typeText ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $movement['quantity'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $movement['date'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $movement['user'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Add functionality for the increment/decrement buttons
document.querySelectorAll('.bg-gray-100').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input[type="number"]');
        if (this.querySelector('svg').classList.contains('arrow-up')) {
            input.value = parseInt(input.value) + 1;
        } else {
            input.value = Math.max(0, parseInt(input.value) - 1);
        }
    });
});

// Add functionality for the update buttons
document.querySelectorAll('.bg-blue-500').forEach(button => {
    button.addEventListener('click', function() {
        const product = this.closest('.border').querySelector('h3').textContent;
        const quantity = this.parentElement.querySelector('input').value;
        alert(`Would update ${product} stock by ${quantity} in a real application`);
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';

// Add these icons to your getIconSvg function if not already present
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'package' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        'arrow-up' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>',
        'arrow-down' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>',
        'box' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>