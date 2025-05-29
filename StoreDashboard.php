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
            --primary: #4361ee;
            --primary-light: #3f37c9;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }

        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.5;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
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
            padding: 2rem;
            background: #f8fafc;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            padding: 1.75rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        /* Stats */
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray);
            margin-bottom: 0.75rem;
            display: block;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .stat-change.positive {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .stat-change.negative {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        /* Products */
        .product-card {
            border: 1px solid var(--light-gray);
            border-radius: 10px;
            padding: 1.25rem;
            transition: all 0.3s ease;
            background: white;
        }

        .product-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(67, 97, 238, 0.1), 0 2px 4px -1px rgba(67, 97, 238, 0.06);
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .product-category {
            font-size: 0.75rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .product-stock {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .stock-label {
            color: var(--gray);
        }

        .stock-value {
            font-weight: 600;
            color: var(--dark);
        }

        /* Forms */
        .input-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .input-number {
            width: 70px;
            text-align: center;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .input-number:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }

        .btn {
            padding: 0.625rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            border-radius: 8px;
            background: var(--light);
            color: var(--gray);
            border: 1px solid var(--light-gray);
        }

        .btn-icon:hover {
            background: #f1f3f5;
            color: var(--dark);
        }

        /* Search */
        .search-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Grid */
        .grid {
            display: grid;
            gap: 1.5rem;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, 1fr);
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        @media (max-width: 1024px) {
            .grid-cols-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .grid-cols-2, .grid-cols-3 {
                grid-template-columns: repeat(1, 1fr);
            }
            
            .content-area {
                padding: 1.5rem;
            }
        }

        /* Utility classes */
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-5 { margin-bottom: 1.25rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }

        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }

        .text-sm { font-size: 0.875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }

        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }

        .text-gray-500 { color: var(--gray); }
        .text-gray-600 { color: #4b5563; }
        .text-blue-500 { color: var(--primary); }
        .text-green-500 { color: var(--success); }
        .text-orange-500 { color: var(--warning); }
        .text-red-500 { color: var(--danger); }

        /* Icons */
        .icon {
            width: 1.25rem;
            height: 1.25rem;
            stroke-width: 2;
        }

        .icon-sm {
            width: 1rem;
            height: 1rem;
        }
        
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <div class="mb-8">
                <h1 class="text-2xl font-bold">Warehouse Staff Dashboard</h1>
                <p class="text-gray-500">Manage stock movements and inventory updates</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Stock Movements Card -->
                <div class="card">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="stat-label">Today's Movements</p>
                            <div class="stat-number"><?= $todayMovements ?></div>
                        </div>
                        <div class="p-3 rounded-full bg-blue-50 text-blue-500">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-change <?= $movementPercentage >= 0 ? 'positive' : 'negative' ?>">
                        <?php if ($movementPercentage >= 0): ?>
                            <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        <?php else: ?>
                            <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        <?php endif; ?>
                        <span><?= abs($movementPercentage) ?>% vs yesterday</span>
                    </div>
                </div>

                <!-- Items Received Card -->
                <div class="card">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="stat-label">Items Received</p>
                            <div class="stat-number"><?= $itemsReceived ?></div>
                        </div>
                        <div class="p-3 rounded-full bg-green-50 text-green-500">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-change <?= $itemsReceived >= $yesterdayReceived ? 'positive' : 'negative' ?>">
                        <?php if ($itemsReceived >= $yesterdayReceived): ?>
                            <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        <?php else: ?>
                            <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        <?php endif; ?>
                        <span><?= abs($itemsReceived - $yesterdayReceived) ?> vs yesterday</span>
                    </div>
                </div>

                <!-- Items Dispatched Card -->
                <div class="card">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="stat-label">Items Dispatched</p>
                            <div class="stat-number"><?= $itemsDispatched ?></div>
                        </div>
                        <div class="p-3 rounded-full bg-orange-50 text-orange-500">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-change <?= $itemsDispatched >= $yesterdayDispatched ? 'positive' : 'negative' ?>">
                        <?php if ($itemsDispatched >= $yesterdayDispatched): ?>
                            <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        <?php else: ?>
                            <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        <?php endif; ?>
                        <span><?= abs($itemsDispatched - $yesterdayDispatched) ?> vs yesterday</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stock Update Section -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="card-title">Quick Stock Update</h2>
                  <!--   <div class="p-2 rounded-lg bg-blue-50 text-blue-500">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div> -->
                </div>

                <div class="search-container">
                    <div class="search-icon">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="productSearch" class="search-input" placeholder="Search products...">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="productList">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <h3 class="product-name"><?= htmlspecialchars($product['product_name']) ?></h3>
                        <p class="product-category"><?= htmlspecialchars($product['category_name']) ?></p>
                        
                        <div class="product-stock">
                            <span class="stock-label">Current Stock:</span>
                            <span class="stock-value"><?= $product['quantity'] ?></span>
                        </div>

                        <form method="POST" class="input-group">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            
                            <button type="button" class="btn-icon decrement-btn">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            
                            <input type="number" name="quantity" class="input-number" value="0" min="0">
                            
                            <button type="button" class="btn-icon increment-btn">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                            
                            <button type="submit" name="update_stock" class="btn btn-primary flex-1">
                                Update
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Movements Section -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Stock Movements</h2>
                    <div class="p-2 rounded-lg bg-blue-50 text-blue-500">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
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
                            <td class="font-semibold"><?= htmlspecialchars($movement['product_name']) ?></td>
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
                    const input = this.closest('.input-group').querySelector('input[type="number"]');
                    const step = 1;
                    const min = parseInt(input.min) || 0;
                    
                    if (this.classList.contains('increment-btn')) {
                        input.value = parseInt(input.value) + step;
                    } else {
                        input.value = Math.max(min, parseInt(input.value) - step);
                    }
                    
                    // Trigger animation
                    input.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        input.style.transform = '';
                    }, 200);
                });
            });

            // Live search functionality
            document.getElementById('productSearch').addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.product-card').forEach(card => {
                    const name = card.querySelector('.product-name').textContent.toLowerCase();
                    card.style.display = name.includes(term) ? 'block' : 'none';
                });
            });
            </script>
        </main>
    </div>
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