<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'config.php';

// Fetch all products with their category names
$query = "SELECT p.product_id, p.product_name, c.category_name, p.quantity, p.minimum_stock, p.price 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id
          ORDER BY p.quantity ASC";
$result = mysqli_query($conn, $query);

// Calculate stock status for each product
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $status = 'good';
    if ($row['quantity'] <= 0) {
        $status = 'out';
    } elseif ($row['quantity'] < $row['minimum_stock']) {
        $status = 'low';
    }
    $row['status'] = $status;
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Stock - Jotech Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            padding-left: 250px; /* Adjust based on your sidebar width */
            transition: all 0.3s;
        }
        .content-area {
            padding: 20px;
        }
        .stock-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
            border-left: 5px solid;
        }
        .stock-card:hover {
            transform: translateY(-5px);
        }
        .stock-card.good {
            border-left-color: #28a745;
        }
        .stock-card.low {
            border-left-color: #ffc107;
        }
        .stock-card.out {
            border-left-color: #dc3545;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
        }
        .search-container {
            margin-bottom: 30px;
        }
        .stock-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
        }
        .summary-item h3 {
            font-weight: bold;
        }
        .good-count {
            color: #28a745;
        }
        .low-count {
            color: #ffc107;
        }
        .out-count {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Include your sidebar here -->
    <?php include 'sidebar.php'; ?>

    <div class="content-area">
        <div class="container-fluid">
            <h1 class="mb-4"><i class="bi bi-box-seam"></i> Current Stock</h1>
            
            <!-- Stock Summary -->
            <?php
            $total_products = count($products);
            $good_stock = count(array_filter($products, fn($p) => $p['status'] === 'good'));
            $low_stock = count(array_filter($products, fn($p) => $p['status'] === 'low'));
            $out_stock = count(array_filter($products, fn($p) => $p['status'] === 'out'));
            ?>
            <div class="stock-summary">
                <div class="row">
                    <div class="col-md-3 summary-item">
                        <h3><?php echo $total_products; ?></h3>
                        <p>Total Products</p>
                    </div>
                    <div class="col-md-3 summary-item">
                        <h3 class="good-count"><?php echo $good_stock; ?></h3>
                        <p>Good Stock</p>
                    </div>
                    <div class="col-md-3 summary-item">
                        <h3 class="low-count"><?php echo $low_stock; ?></h3>
                        <p>Low Stock</p>
                    </div>
                    <div class="col-md-3 summary-item">
                        <h3 class="out-count"><?php echo $out_stock; ?></h3>
                        <p>Out of Stock</p>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="search-container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select id="statusFilter" class="form-select">
                            <option value="all">All Status</option>
                            <option value="good">Good Stock</option>
                            <option value="low">Low Stock</option>
                            <option value="out">Out of Stock</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products List -->
            <div class="row" id="productsContainer">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card stock-card <?php echo $product['status']; ?>">
                            <span class="badge bg-<?php 
                                echo $product['status'] === 'good' ? 'success' : 
                                     ($product['status'] === 'low' ? 'warning' : 'danger'); 
                            ?> status-badge">
                                <?php echo strtoupper($product['status']); ?>
                            </span>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($product['category_name']); ?></h6>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <p class="card-text"><strong>Quantity:</strong> <?php echo $product['quantity']; ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="card-text"><strong>Min Stock:</strong> <?php echo $product['minimum_stock']; ?></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <p class="card-text"><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 10px;">
                                    <?php 
                                    $percentage = $product['minimum_stock'] > 0 ? 
                                        min(100, ($product['quantity'] / $product['minimum_stock']) * 100) : 0;
                                    $bg_class = $product['status'] === 'good' ? 'bg-success' : 
                                               ($product['status'] === 'low' ? 'bg-warning' : 'bg-danger');
                                    ?>
                                    <div class="progress-bar <?php echo $bg_class; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $percentage; ?>%" 
                                         aria-valuenow="<?php echo $percentage; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    No products found in stock. Please add some products.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search and filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const productsContainer = document.getElementById('productsContainer');
            const productCards = productsContainer.querySelectorAll('.col-md-4');

            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;

                productCards.forEach(card => {
                    const productName = card.querySelector('.card-title').textContent.toLowerCase();
                    const productStatus = card.querySelector('.stock-card').classList.contains('good') ? 'good' : 
                                         card.querySelector('.stock-card').classList.contains('low') ? 'low' : 'out';
                    
                    const matchesSearch = productName.includes(searchTerm);
                    const matchesStatus = statusValue === 'all' || productStatus === statusValue;
                    
                    if (matchesSearch && matchesStatus) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterProducts);
            statusFilter.addEventListener('change', filterProducts);
        });
    </script>
</body>
</html>