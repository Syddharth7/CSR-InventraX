<?php
// api/sales_handler.php - Handle all sales operations
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create_sale':
        createSale();
        break;
    case 'get_sales':
        getSales();
        break;
    case 'get_sale_details':
        getSaleDetails();
        break;
    case 'process_return':
        processReturn();
        break;
    case 'get_sales_summary':
        getSalesSummary();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function createSale() {
    global $conn;
    
    $items = json_decode($_POST['items'] ?? '[]', true);
    $payment_method = sanitizeInput($_POST['payment_method'] ?? 'cash');
    $customer_name = sanitizeInput($_POST['customer_name'] ?? '');
    
    if (empty($items)) {
        echo json_encode(['success' => false, 'message' => 'No items in cart']);
        return;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        $total_amount = 0;
        $sale_ids = [];
        
        foreach ($items as $item) {
            $product_id = (int)$item['id'];
            $quantity = (int)$item['quantity'];
            $unit_price = (float)$item['price'];
            
            // Check stock availability
            $check = $conn->query("SELECT quantity, product_name FROM products WHERE product_id = $product_id");
            if ($check->num_rows == 0) {
                throw new Exception("Product ID $product_id not found");
            }
            
            $product = $check->fetch_assoc();
            if ($product['quantity'] < $quantity) {
                throw new Exception("Insufficient stock for {$product['product_name']}");
            }
            
            $item_total = $unit_price * $quantity;
            $total_amount += $item_total;
            
            // Insert sale record
            $stmt = $conn->prepare("INSERT INTO sales 
                    (product_id, user_id, quantity, unit_price, total_amount, payment_method, customer_name) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiddss", 
                $product_id, $_SESSION['user_id'], $quantity, 
                $unit_price, $item_total, $payment_method, $customer_name
            );
            $stmt->execute();
            $sale_ids[] = $stmt->insert_id;
            
            // Update product quantity
            $new_quantity = $product['quantity'] - $quantity;
            $conn->query("UPDATE products SET quantity = $new_quantity WHERE product_id = $product_id");
            
            // Record stock movement
            $move_stmt = $conn->prepare("INSERT INTO stock_movements 
                    (product_id, user_id, movement_type, quantity, reference_id, notes) 
                    VALUES (?, ?, 'stock_out', ?, ?, 'Sale transaction')");
            $sale_id = $stmt->insert_id;
            $move_stmt->bind_param("iiii", $product_id, $_SESSION['user_id'], $quantity, $sale_id);
            $move_stmt->execute();
            
            // Check for low stock and create notification
            $reorder = $conn->query("SELECT reorder_level FROM products WHERE product_id = $product_id")->fetch_assoc();
            if ($new_quantity <= $reorder['reorder_level']) {
                createNotification('low_stock', 
                    "{$product['product_name']} is running low. Only $new_quantity units remaining.", 
                    null);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Log activity
        logActivity($_SESSION['user_id'], 'Sale completed', 'Sales', 
                   "Total: ₱" . number_format($total_amount, 2) . ", Items: " . count($items));
        
        echo json_encode([
            'success' => true, 
            'message' => 'Sale completed successfully',
            'sale_ids' => $sale_ids,
            'total_amount' => $total_amount
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getSales() {
    global $conn;
    
    $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    $limit = (int)($_GET['limit'] ?? 50);
    
    $sql = "SELECT s.*, p.product_name, p.sku, u.full_name as seller_name
            FROM sales s
            JOIN products p ON s.product_id = p.product_id
            JOIN users u ON s.user_id = u.user_id
            WHERE DATE(s.sale_date) BETWEEN ? AND ?
            ORDER BY s.sale_date DESC
            LIMIT $limit";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sales = [];
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
    
    echo json_encode(['success' => true, 'sales' => $sales]);
}

function getSaleDetails() {
    global $conn;
    
    $sale_id = (int)($_GET['id'] ?? 0);
    
    if ($sale_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid sale ID']);
        return;
    }
    
    $sql = "SELECT s.*, p.product_name, p.sku, u.full_name as seller_name
            FROM sales s
            JOIN products p ON s.product_id = p.product_id
            JOIN users u ON s.user_id = u.user_id
            WHERE s.sale_id = $sale_id";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $sale = $result->fetch_assoc();
        echo json_encode(['success' => true, 'sale' => $sale]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sale not found']);
    }
}

function processReturn() {
    global $conn;
    
    $sale_id = (int)($_POST['sale_id'] ?? 0);
    $return_quantity = (int)($_POST['return_quantity'] ?? 0);
    $reason = sanitizeInput($_POST['reason'] ?? '');
    
    if ($sale_id <= 0 || $return_quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        return;
    }
    
    // Get sale details
    $result = $conn->query("SELECT * FROM sales WHERE sale_id = $sale_id");
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Sale not found']);
        return;
    }
    
    $sale = $result->fetch_assoc();
    
    if ($return_quantity > $sale['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Return quantity exceeds sale quantity']);
        return;
    }
    
    $conn->begin_transaction();
    
    try {
        // Update product quantity (return to stock)
        $conn->query("UPDATE products SET quantity = quantity + $return_quantity 
                     WHERE product_id = {$sale['product_id']}");
        
        // Record stock movement
        $stmt = $conn->prepare("INSERT INTO stock_movements 
                (product_id, user_id, movement_type, quantity, reference_id, notes) 
                VALUES (?, ?, 'return', ?, ?, ?)");
        $stmt->bind_param("iiiis", 
            $sale['product_id'], $_SESSION['user_id'], $return_quantity, $sale_id, $reason
        );
        $stmt->execute();
        
        // Update sale record or mark as returned
        if ($return_quantity == $sale['quantity']) {
            // Full return - could add a status field
            $conn->query("UPDATE sales SET notes = CONCAT(IFNULL(notes, ''), ' [RETURNED: $reason]') 
                         WHERE sale_id = $sale_id");
        } else {
            // Partial return
            $new_quantity = $sale['quantity'] - $return_quantity;
            $new_total = $sale['unit_price'] * $new_quantity;
            $conn->query("UPDATE sales SET 
                         quantity = $new_quantity, 
                         total_amount = $new_total,
                         notes = CONCAT(IFNULL(notes, ''), ' [PARTIAL RETURN: $return_quantity units - $reason]')
                         WHERE sale_id = $sale_id");
        }
        
        $conn->commit();
        
        logActivity($_SESSION['user_id'], 'Sale return processed', 'Sales', 
                   "Sale ID: $sale_id, Quantity: $return_quantity");
        
        echo json_encode(['success' => true, 'message' => 'Return processed successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getSalesSummary() {
    global $conn;
    
    $period = $_GET['period'] ?? 'today';
    
    switch ($period) {
        case 'today':
            $date_condition = "DATE(sale_date) = CURDATE()";
            break;
        case 'week':
            $date_condition = "YEARWEEK(sale_date) = YEARWEEK(CURDATE())";
            break;
        case 'month':
            $date_condition = "YEAR(sale_date) = YEAR(CURDATE()) AND MONTH(sale_date) = MONTH(CURDATE())";
            break;
        case 'year':
            $date_condition = "YEAR(sale_date) = YEAR(CURDATE())";
            break;
        default:
            $date_condition = "DATE(sale_date) = CURDATE()";
    }
    
    // Total sales
    $total_result = $conn->query("SELECT 
            COUNT(*) as total_transactions,
            SUM(total_amount) as total_revenue,
            SUM(quantity) as total_items_sold
            FROM sales 
            WHERE $date_condition");
    $totals = $total_result->fetch_assoc();
    
    // Top selling products
    $top_products = [];
    $top_result = $conn->query("SELECT 
            p.product_name, 
            SUM(s.quantity) as units_sold,
            SUM(s.total_amount) as revenue
            FROM sales s
            JOIN products p ON s.product_id = p.product_id
            WHERE $date_condition
            GROUP BY s.product_id
            ORDER BY units_sold DESC
            LIMIT 5");
    
    while ($row = $top_result->fetch_assoc()) {
        $top_products[] = $row;
    }
    
    // Sales by payment method
    $payment_methods = [];
    $payment_result = $conn->query("SELECT 
            payment_method,
            COUNT(*) as count,
            SUM(total_amount) as total
            FROM sales
            WHERE $date_condition
            GROUP BY payment_method");
    
    while ($row = $payment_result->fetch_assoc()) {
        $payment_methods[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'summary' => $totals,
        'top_products' => $top_products,
        'payment_methods' => $payment_methods
    ]);
}
?>