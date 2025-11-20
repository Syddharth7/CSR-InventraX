<?php
// admin/reports.php - Advanced Reporting System
require_once '../config.php';
requireAdmin();

// Report generation function
function generateSalesReport($start_date, $end_date, $format = 'html') {
    global $conn;
    
    $sql = "SELECT 
                s.sale_id,
                s.sale_date,
                p.product_name,
                p.sku,
                s.quantity,
                s.unit_price,
                s.total_amount,
                s.payment_method,
                s.customer_name,
                u.full_name as seller_name
            FROM sales s
            JOIN products p ON s.product_id = p.product_id
            JOIN users u ON s.user_id = u.user_id
            WHERE DATE(s.sale_date) BETWEEN ? AND ?
            ORDER BY s.sale_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    $total_revenue = 0;
    $total_transactions = 0;
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $total_revenue += $row['total_amount'];
        $total_transactions++;
    }
    
    if ($format == 'csv') {
        return generateCSV($data, 'sales_report');
    } elseif ($format == 'excel') {
        return generateExcel($data, 'sales_report');
    } else {
        return [
            'data' => $data,
            'summary' => [
                'total_revenue' => $total_revenue,
                'total_transactions' => $total_transactions,
                'average_sale' => $total_transactions > 0 ? $total_revenue / $total_transactions : 0
            ]
        ];
    }
}

function generateInventoryReport($format = 'html') {
    global $conn;
    
    $sql = "SELECT 
                p.product_id,
                p.product_name,
                p.sku,
                c.category_name,
                p.quantity,
                p.reorder_level,
                p.price,
                p.cost,
                (p.quantity * p.price) as inventory_value,
                p.status,
                CASE 
                    WHEN p.quantity = 0 THEN 'Out of Stock'
                    WHEN p.quantity <= p.reorder_level THEN 'Low Stock'
                    ELSE 'In Stock'
                END as stock_status
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            ORDER BY p.quantity ASC";
    
    $result = $conn->query($sql);
    $data = [];
    $total_inventory_value = 0;
    $low_stock_count = 0;
    $out_of_stock_count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $total_inventory_value += $row['inventory_value'];
        
        if ($row['quantity'] == 0) {
            $out_of_stock_count++;
        } elseif ($row['quantity'] <= $row['reorder_level']) {
            $low_stock_count++;
        }
    }
    
    if ($format == 'csv') {
        return generateCSV($data, 'inventory_report');
    } elseif ($format == 'excel') {
        return generateExcel($data, 'inventory_report');
    } else {
        return [
            'data' => $data,
            'summary' => [
                'total_products' => count($data),
                'total_inventory_value' => $total_inventory_value,
                'low_stock_count' => $low_stock_count,
                'out_of_stock_count' => $out_of_stock_count
            ]
        ];
    }
}

function generateProfitReport($start_date, $end_date) {
    global $conn;
    
    $sql = "SELECT 
                DATE(s.sale_date) as sale_date,
                SUM(s.total_amount) as revenue,
                SUM(s.quantity * p.cost) as cost,
                SUM(s.total_amount - (s.quantity * p.cost)) as profit,
                COUNT(DISTINCT s.sale_id) as transactions
            FROM sales s
            JOIN products p ON s.product_id = p.product_id
            WHERE DATE(s.sale_date) BETWEEN ? AND ?
            GROUP BY DATE(s.sale_date)
            ORDER BY sale_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    $total_profit = 0;
    
    while ($row = $result->fetch_assoc()) {
        $row['profit_margin'] = ($row['revenue'] > 0) ? 
            (($row['profit'] / $row['revenue']) * 100) : 0;
        $data[] = $row;
        $total_profit += $row['profit'];
    }
    
    return [
        'data' => $data,
        'total_profit' => $total_profit
    ];
}

function generateCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    if (!empty($data)) {
        // Write headers
        fputcsv($output, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit;
}

function generateExcel($data, $filename) {
    // Simple Excel generation using HTML table with .xls extension
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.xls"');
    
    echo '<table border="1">';
    
    if (!empty($data)) {
        // Headers
        echo '<tr>';
        foreach (array_keys($data[0]) as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';
        
        // Data
        foreach ($data as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo '<td>' . htmlspecialchars($cell) . '</td>';
            }
            echo '</tr>';
        }
    }
    
    echo '</table>';
    exit;
}

// Handle export requests
if (isset($_GET['export'])) {
    $report_type = $_GET['report_type'] ?? 'sales';
    $format = $_GET['format'] ?? 'csv';
    $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    
    switch ($report_type) {
        case 'sales':
            generateSalesReport($start_date, $end_date, $format);
            break;
        case 'inventory':
            generateInventoryReport($format);
            break;
        case 'profit':
            $profit_data = generateProfitReport($start_date, $end_date);
            if ($format == 'csv') {
                generateCSV($profit_data['data'], 'profit_report');
            } else {
                generateExcel($profit_data['data'], 'profit_report');
            }
            break;
    }
}

// Get report data for display
$sales_report = null;
$inventory_report = null;

if (isset($_POST['generate_report'])) {
    $report_type = $_POST['report_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    if ($report_type == 'sales') {
        $sales_report = generateSalesReport($start_date, $end_date, 'html');
    } elseif ($report_type == 'inventory') {
        $inventory_report = generateInventoryReport('html');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - InventraX</title>
    <!-- Include dashboard styles -->
</head>
<body>
    <div class="content-area">
        <h1>Report Generator</h1>
        
        <div class="card">
            <h2>Generate Report</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Report Type</label>
                    <select name="report_type" class="form-control" required>
                        <option value="sales">Sales Report</option>
                        <option value="inventory">Inventory Report</option>
                        <option value="profit">Profit Analysis</option>
                        <option value="product_performance">Product Performance</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="<?= date('Y-m-d', strtotime('-30 days')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                
                <button type="submit" name="generate_report" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i> Generate Report
                </button>
            </form>
        </div>
        
        <?php if ($sales_report): ?>
        <div class="card">
            <div class="card-header">
                <h2>Sales Report</h2>
                <div>
                    <a href="?export=1&report_type=sales&format=csv&start_date=<?= $_POST['start_date'] ?>&end_date=<?= $_POST['end_date'] ?>" 
                       class="btn btn-sm">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <a href="?export=1&report_type=sales&format=excel&start_date=<?= $_POST['start_date'] ?>&end_date=<?= $_POST['end_date'] ?>" 
                       class="btn btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            
            <div class="summary-cards">
                <div class="summary-card">
                    <h3>Total Revenue</h3>
                    <p><?= formatCurrency($sales_report['summary']['total_revenue']) ?></p>
                </div>
                <div class="summary-card">
                    <h3>Total Transactions</h3>
                    <p><?= number_format($sales_report['summary']['total_transactions']) ?></p>
                </div>
                <div class="summary-card">
                    <h3>Average Sale</h3>
                    <p><?= formatCurrency($sales_report['summary']['average_sale']) ?></p>
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Seller</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales_report['data'] as $sale): ?>
                    <tr>
                        <td><?= formatDateTime($sale['sale_date']) ?></td>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td><?= htmlspecialchars($sale['sku']) ?></td>
                        <td><?= $sale['quantity'] ?></td>
                        <td><?= formatCurrency($sale['unit_price']) ?></td>
                        <td><?= formatCurrency($sale['total_amount']) ?></td>
                        <td><?= ucfirst($sale['payment_method']) ?></td>
                        <td><?= htmlspecialchars($sale['seller_name']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <?php if ($inventory_report): ?>
        <div class="card">
            <div class="card-header">
                <h2>Inventory Report</h2>
                <div>
                    <a href="?export=1&report_type=inventory&format=csv" class="btn btn-sm">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <a href="?export=1&report_type=inventory&format=excel" class="btn btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            
            <div class="summary-cards">
                <div class="summary-card">
                    <h3>Total Products</h3>
                    <p><?= $inventory_report['summary']['total_products'] ?></p>
                </div>
                <div class="summary-card">
                    <h3>Inventory Value</h3>
                    <p><?= formatCurrency($inventory_report['summary']['total_inventory_value']) ?></p>
                </div>
                <div class="summary-card">
                    <h3>Low Stock Items</h3>
                    <p class="text-warning"><?= $inventory_report['summary']['low_stock_count'] ?></p>
                </div>
                <div class="summary-card">
                    <h3>Out of Stock</h3>
                    <p class="text-danger"><?= $inventory_report['summary']['out_of_stock_count'] ?></p>
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Reorder Level</th>
                        <th>Price</th>
                        <th>Inventory Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory_report['data'] as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td><?= htmlspecialchars($product['sku']) ?></td>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td><?= $product['reorder_level'] ?></td>
                        <td><?= formatCurrency($product['price']) ?></td>
                        <td><?= formatCurrency($product['inventory_value']) ?></td>
                        <td>
                            <span class="badge badge-<?= $product['stock_status'] == 'In Stock' ? 'success' : 
                                ($product['stock_status'] == 'Low Stock' ? 'warning' : 'danger') ?>">
                                <?= $product['stock_status'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>