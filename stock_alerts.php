<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'config.php';

// Fetch products with low stock (quantity < minimum_stock) or out of stock
$query = "SELECT p.product_id, p.product_name, c.category_name, p.quantity, p.minimum_stock, p.price 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id
          WHERE p.quantity <= p.minimum_stock
          ORDER BY 
            CASE 
                WHEN p.quantity = 0 THEN 0  -- Out of stock first
                WHEN p.quantity < p.minimum_stock THEN 1  -- Low stock next
                ELSE 2
            END,
            p.quantity ASC";
$result = mysqli_query($conn, $query);

$alerts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $status = ($row['quantity'] == 0) ? 'out' : 'low';
    $urgency = ($status == 'out') ? 1 : 2; // Higher urgency for out of stock
    $row['status'] = $status;
    $row['urgency'] = $urgency;
    $alerts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Alerts - Jotech Inventory</title>
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
        .alert-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
            border-left: 5px solid;
            position: relative;
            overflow: hidden;
        }
        .alert-card.low {
            border-left-color: #ffc107;
            background-color: rgba(255, 193, 7, 0.05);
        }
        .alert-card.out {
            border-left-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }
        .alert-card:hover {
            transform: translateY(-5px);
        }
        .alert-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
        }
        .alert-urgency {
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background-color: #dc3545;
        }
        .alert-urgency.low {
            background-color: #ffc107;
        }
        .alert-urgency.out {
            background-color: #dc3545;
        }
        .alert-header {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .action-buttons {
            margin-top: 15px;
        }
        .empty-alerts {
            text-align: center;
            padding: 50px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .priority-icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Include your sidebar here -->
    <?php include 'sidebar.php'; ?>

    <div class="content-area">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-exclamation-triangle"></i> Stock Alerts</h1>
             <!--    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#emailModal">
                    <i class="bi bi-envelope"></i> Send Alerts Report
                </button> -->
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-warning bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-warning"><i class="bi bi-exclamation-circle"></i> Low Stock Items</h5>
                                    <h2 class="mb-0"><?php echo count(array_filter($alerts, fn($a) => $a['status'] === 'low')); ?></h2>
                                </div>
                                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-danger bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-danger"><i class="bi bi-x-circle"></i> Out of Stock Items</h5>
                                    <h2 class="mb-0"><?php echo count(array_filter($alerts, fn($a) => $a['status'] === 'out')); ?></h2>
                                </div>
                                <i class="bi bi-x-octagon-fill text-danger" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts List -->
            <?php if (!empty($alerts)): ?>
                <div class="row">
                    <?php foreach ($alerts as $alert): ?>
                        <div class="col-md-6">
                            <div class="card alert-card <?php echo $alert['status']; ?>">
                                <div class="alert-urgency <?php echo $alert['status']; ?>"></div>
                                <span class="badge bg-<?php echo $alert['status'] === 'low' ? 'warning' : 'danger'; ?> alert-badge">
                                    <?php echo strtoupper($alert['status'] === 'low' ? 'Low Stock' : 'Out of Stock'); ?>
                                </span>
                                <div class="card-body">
                                    <div class="alert-header">
                                        <h5 class="card-title">
                                            <?php if ($alert['urgency'] == 1): ?>
                                                <i class="bi bi-exclamation-octagon-fill text-danger priority-icon"></i>
                                            <?php else: ?>
                                                <i class="bi bi-exclamation-triangle-fill text-warning priority-icon"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($alert['product_name']); ?>
                                        </h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($alert['category_name']); ?></h6>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Current Quantity:</strong> 
                                                <span class="<?php echo $alert['status'] === 'low' ? 'text-warning' : 'text-danger'; ?>">
                                                    <?php echo $alert['quantity']; ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Minimum Required:</strong> <?php echo $alert['minimum_stock']; ?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Price:</strong> $<?php echo number_format($alert['price'], 2); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Deficit:</strong> 
                                                <span class="<?php echo $alert['status'] === 'low' ? 'text-warning' : 'text-danger'; ?>">
                                                    <?php echo max(0, $alert['minimum_stock'] - $alert['quantity']); ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <a href="update_stock.php?product_id=<?php echo $alert['product_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle"></i> Update Stock
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#historyModal<?php echo $alert['product_id']; ?>">
                                            <i class="bi bi-clock-history"></i> View History
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- History Modal for each product -->
                        <div class="modal fade" id="historyModal<?php echo $alert['product_id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Stock History: <?php echo htmlspecialchars($alert['product_name']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        $historyQuery = "SELECT * FROM stock_update 
                                                        WHERE product_id = {$alert['product_id']}
                                                        ORDER BY created_at DESC
                                                        LIMIT 10";
                                        $historyResult = mysqli_query($conn, $historyQuery);
                                        
                                        if (mysqli_num_rows($historyResult) > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Type</th>
                                                            <th>Quantity</th>
                                                            <th>User</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($history = mysqli_fetch_assoc($historyResult)): ?>
                                                            <tr>
                                                                <td><?php echo date('M d, Y H:i', strtotime($history['created_at'])); ?></td>
                                                                <td>
                                                                    <span class="badge bg-<?php 
                                                                        echo $history['update_type'] === 'increment' || $history['update_type'] === 'in' ? 'success' : 'danger'; 
                                                                    ?>">
                                                                        <?php echo ucfirst($history['update_type']); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo $history['quantity']; ?></td>
                                                                <td>
                                                                    <?php 
                                                                    $userQuery = "SELECT user_name FROM users WHERE user_id = {$history['user_id']}";
                                                                    $userResult = mysqli_query($conn, $userQuery);
                                                                    echo mysqli_num_rows($userResult) > 0 ? mysqli_fetch_assoc($userResult)['user_name'] : 'Unknown';
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p>No stock history available for this product.</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-alerts">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">No Stock Alerts</h3>
                    <p class="text-muted">All products are currently above their minimum stock levels.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Email Report Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="send_alerts_report.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Alerts Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="recipientEmail" class="form-label">Recipient Email</label>
                            <input type="email" class="form-control" id="recipientEmail" name="recipient_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" name="subject" 
                                   value="Stock Alerts Report - <?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Additional Message</label>
                            <textarea class="form-control" id="emailMessage" name="message" rows="3"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeAll" name="include_all">
                            <label class="form-check-label" for="includeAll">
                                Include all products (not just alerts)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh the page every 5 minutes to check for new alerts
        setTimeout(function(){
            window.location.reload();
        }, 300000); // 300000 ms = 5 minutes
    </script>
</body>
</html>