<?php
session_start();
require_once 'config.php';
$title = "Staff Dashboard";

// Get today's stock movements count
$todayMovements = 0;
$yesterdayMovements = 0;
try {
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Today's movements
    $stmt = $conn->prepare("SELECT COUNT(*) FROM stock_update WHERE DATE(created_at) = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $todayMovements = $stmt->get_result()->fetch_row()[0];
    $stmt->close();
    
    // Yesterday's movements
    $stmt = $conn->prepare("SELECT COUNT(*) FROM stock_update WHERE DATE(created_at) = ?");
    $stmt->bind_param("s", $yesterday);
    $stmt->execute();
    $yesterdayMovements = $stmt->get_result()->fetch_row()[0];
    $stmt->close();
    
    // Calculate percentage difference
    $movementPercentage = $yesterdayMovements > 0 ? 
        round((($todayMovements - $yesterdayMovements) / $yesterdayMovements)) * 100 : 0;
    
} catch (Exception $e) {
    error_log("Error getting movement stats: " . $e->getMessage());
}

// Get items received/dispatched today
$itemsReceived = 0;
$itemsDispatched = 0;
$yesterdayReceived = 0;
$yesterdayDispatched = 0;
try {
    // Today's received
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM stock_update WHERE update_type = 'in' AND DATE(created_at) = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $itemsReceived = $stmt->get_result()->fetch_row()[0] ?? 0;
    $stmt->close();
    
    // Today's dispatched
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM stock_update WHERE update_type = 'out' AND DATE(created_at) = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $itemsDispatched = $stmt->get_result()->fetch_row()[0] ?? 0;
    $stmt->close();
    
    // Yesterday's received
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM stock_update WHERE update_type = 'in' AND DATE(created_at) = ?");
    $stmt->bind_param("s", $yesterday);
    $stmt->execute();
    $yesterdayReceived = $stmt->get_result()->fetch_row()[0] ?? 0;
    $stmt->close();
    
    // Yesterday's dispatched
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM stock_update WHERE update_type = 'out' AND DATE(created_at) = ?");
    $stmt->bind_param("s", $yesterday);
    $stmt->execute();
    $yesterdayDispatched = $stmt->get_result()->fetch_row()[0] ?? 0;
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error getting received/dispatched stats: " . $e->getMessage());
}

// Handle stock update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $productId = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $updateType = $quantity >= 0 ? 'in' : 'out';
    $quantity = abs($quantity);
    $userId = $_SESSION['user_id'];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Update product quantity
        $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE product_id = ?");
        $updateValue = ($updateType === 'in') ? $quantity : -$quantity;
        $stmt->bind_param("ii", $updateValue, $productId);
        $stmt->execute();
        $stmt->close();
        
        // Record in stock_update
        $stmt = $conn->prepare("INSERT INTO stock_update (update_type, product_id, quantity, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siii", $updateType, $productId, $quantity, $userId);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        $_SESSION['success'] = "Stock updated successfully!";
        header("Refresh:0");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error updating stock: " . $e->getMessage();
        error_log("Stock update error: " . $e->getMessage());
    }
}

// Get products for quick update section
$products = [];
try {
    $stmt = $conn->prepare("SELECT p.product_id, p.product_name, p.quantity, c.category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.category_id
                          ORDER BY p.product_name");
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Error getting products: " . $e->getMessage());
}

// Get recent stock movements
$recentMovements = [];
try {
    $stmt = $conn->prepare("SELECT su.update_id, p.product_name, su.update_type, su.quantity, 
                          DATE_FORMAT(su.created_at, '%Y-%m-%d') as date, u.user_name
                          FROM stock_update su
                          JOIN products p ON su.product_id = p.product_id
                          JOIN users u ON su.user_id = u.user_id
                          ORDER BY su.created_at DESC
                          LIMIT 5");
    $stmt->execute();
    $result = $stmt->get_result();
    $recentMovements = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Error getting recent movements: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Warehouse Management</title>
    <style>
        :root {
            --primary: #3B82F6;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --light: #F8FAFC;
            --dark: #1E293B;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #F1F5F9;
            color: #334155;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--dark);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .product-card {
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: box-shadow 0.2s;
        }

        .product-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .input-number {
            width: 4rem;
            text-align: center;
            border: 1px solid #CBD5E1;
            border-radius: 0.375rem;
            padding: 0.375rem 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563EB;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        th {
            background-color: #F8FAFC;
            color: #64748B;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            text-align: left;
        }

        td {
            padding: 1rem 1.5rem;
            border-top: 1px solid #F1F5F9;
        }
    </style>
</head>
<body class="dashboard-container">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Warehouse Staff Dashboard</h1>
        <p class="text-gray-600 mt-1">Manage stock movements and inventory updates</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Stock Movements Card -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500">Today's Movements</h3>
                <div class="text-blue-500">
                    <?= getIconSvg('package', 'icon') ?>
                </div>
            </div>
            <div class="stat-number mb-2"><?= $todayMovements ?></div>
            <div class="flex items-center text-sm <?= $movementPercentage >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $movementPercentage >= 0 ? getIconSvg('arrow-up', 'icon mr-1') : getIconSvg('arrow-down', 'icon mr-1') ?>
                <span><?= abs($movementPercentage) ?>% vs yesterday</span>
            </div>
        </div>

        <!-- Items Received Card -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500">Items Received</h3>
                <div class="text-green-500">
                    <?= getIconSvg('arrow-down', 'icon') ?>
                </div>
            </div>
            <div class="stat-number mb-2"><?= $itemsReceived ?></div>
            <div class="flex items-center text-sm <?= $itemsReceived >= $yesterdayReceived ? 'text-green-600' : 'text-red-600' ?>">
                <?= $itemsReceived >= $yesterdayReceived ? getIconSvg('arrow-up', 'icon mr-1') : getIconSvg('arrow-down', 'icon mr-1') ?>
                <span><?= abs($itemsReceived - $yesterdayReceived) ?> vs yesterday</span>
            </div>
        </div>

        <!-- Items Dispatched Card -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500">Items Dispatched</h3>
                <div class="text-orange-500">
                    <?= getIconSvg('arrow-up', 'icon') ?>
                </div>
            </div>
            <div class="stat-number mb-2"><?= $itemsDispatched ?></div>
            <div class="flex items-center text-sm <?= $itemsDispatched >= $yesterdayDispatched ? 'text-green-600' : 'text-red-600' ?>">
                <?= $itemsDispatched >= $yesterdayDispatched ? getIconSvg('arrow-up', 'icon mr-1') : getIconSvg('arrow-down', 'icon mr-1') ?>
                <span><?= abs($itemsDispatched - $yesterdayDispatched) ?> vs yesterday</span>
            </div>
        </div>
    </div>

    <!-- Quick Stock Update Section -->
    <div class="card mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">Quick Stock Update</h2>
            <div class="text-blue-500">
                <?= getIconSvg('edit', 'icon') ?>
            </div>
        </div>

        <div class="mb-4 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <?= getIconSvg('search', 'icon') ?>
            </div>
            <input type="text" id="productSearch" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Search products...">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="productList">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold"><?= htmlspecialchars($product['product_name']) ?></h3>
                    <span class="text-sm text-gray-500"><?= htmlspecialchars($product['category_name']) ?></span>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-gray-600">Current Stock:</span>
                    <span class="font-medium"><?= $product['quantity'] ?></span>
                </div>

                <form method="POST" class="flex items-center gap-2">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                    
                    <button type="button" class="p-1.5 rounded-lg border hover:bg-gray-50 decrement-btn">
                        <?= getIconSvg('minus', 'icon') ?>
                    </button>
                    
                    <input type="number" name="quantity" 
                           class="input-number" 
                           value="0" min="0">
                    
                    <button type="button" class="p-1.5 rounded-lg border hover:bg-gray-50 increment-btn">
                        <?= getIconSvg('plus', 'icon') ?>
                    </button>
                    
                    <button type="submit" name="update_stock" 
                            class="btn btn-primary flex-1">
                        Update
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Movements Section -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Recent Stock Movements</h2>
            <div class="text-blue-500">
                <?= getIconSvg('activity', 'icon') ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentMovements as $movement): ?>
                <tr>
                    <td class="font-medium"><?= htmlspecialchars($movement['product_name']) ?></td>
                    <td>
                        <span class="badge <?= $movement['update_type'] === 'in' ? 'badge-success' : 'badge-warning' ?>">
                            <?= $movement['update_type'] === 'in' ? 'Received' : 'Dispatched' ?>
                        </span>
                    </td>
                    <td><?= $movement['quantity'] ?></td>
                    <td><?= $movement['date'] ?></td>
                    <td><?= htmlspecialchars($movement['user_name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Enhanced number input controls
    document.querySelectorAll('.increment-btn, .decrement-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input[type="number"]');
            const step = 1;
            const min = parseInt(input.min) || 0;
            
            if (this.classList.contains('increment-btn')) {
                input.value = parseInt(input.value) + step;
            } else {
                input.value = Math.max(min, parseInt(input.value) - step);
            }
        });
    });

    // Live search functionality
    document.getElementById('productSearch').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            card.style.display = name.includes(term) ? 'block' : 'none';
        });
    });
    </script>
</body>
</html>

<?php
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'package' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>',
        'arrow-up' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>',
        'arrow-down' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>',
        'edit' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>',
        'search' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>',
        'activity' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>',
        'plus' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>',
        'minus' => '<svg class="'.$classes.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>'
    ];
    return $icons[$iconName] ?? '';
}
?>