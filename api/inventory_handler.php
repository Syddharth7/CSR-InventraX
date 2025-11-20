<?php
// api/inventory_handler.php - Handle all inventory operations
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_all_products':
        getAllProducts();
        break;
    case 'get_product':
        getProduct();
        break;
    case 'add_product':
        addProduct();
        break;
    case 'update_product':
        updateProduct();
        break;
    case 'delete_product':
        deleteProduct();
        break;
    case 'stock_movement':
        recordStockMovement();
        break;
    case 'get_categories':
        getCategories();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getAllProducts() {
    global $conn;
    
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE 1=1";
    
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (p.product_name LIKE '%$search%' OR p.sku LIKE '%$search%')";
    }
    
    if (!empty($category)) {
        $category = (int)$category;
        $sql .= " AND p.category_id = $category";
    }
    
    if (!empty($status)) {
        $status = $conn->real_escape_string($status);
        $sql .= " AND p.status = '$status'";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $result = $conn->query($sql);
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        // Determine stock level
        if ($row['quantity'] == 0) {
            $row['stock_level'] = 'out';
        } elseif ($row['quantity'] <= $row['reorder_level']) {
            $row['stock_level'] = 'low';
        } elseif ($row['quantity'] <= $row['reorder_level'] * 2) {
            $row['stock_level'] = 'medium';
        } else {
            $row['stock_level'] = 'high';
        }
        
        $products[] = $row;
    }
    
    echo json_encode(['success' => true, 'products' => $products]);
}

function getProduct() {
    global $conn;
    
    $product_id = (int)($_GET['id'] ?? 0);
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = $product_id";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
}

function addProduct() {
    global $conn;
    
    $product_name = sanitizeInput($_POST['product_name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = sanitizeInput($_POST['description'] ?? '');
    $sku = sanitizeInput($_POST['sku'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $cost = (float)($_POST['cost'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $reorder_level = (int)($_POST['reorder_level'] ?? 10);
    $supplier_name = sanitizeInput($_POST['supplier_name'] ?? '');
    $supplier_contact = sanitizeInput($_POST['supplier_contact'] ?? '');
    
    // Validation
    if (empty($product_name) || empty($sku) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        return;
    }
    
    // Check if SKU already exists
    $check = $conn->prepare("SELECT product_id FROM products WHERE sku = ?");
    $check->bind_param("s", $sku);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'SKU already exists']);
        return;
    }
    
    // Handle image upload
    $image = 'no-image.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadProductImage($_FILES['image']);
        if (!$image) {
            echo json_encode(['success' => false, 'message' => 'Image upload failed']);
            return;
        }
    }
    
    // Insert product
    $stmt = $conn->prepare("INSERT INTO products 
            (product_name, category_id, description, sku, price, cost, quantity, 
             reorder_level, image, supplier_name, supplier_contact) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sissddissss", 
        $product_name, $category_id, $description, $sku, $price, $cost, 
        $quantity, $reorder_level, $image, $supplier_name, $supplier_contact
    );
    
    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        
        // Log activity
        logActivity($_SESSION['user_id'], 'Added new product', 'Inventory', 
                   "Product: $product_name (ID: $product_id)");
        
        // Create stock movement record
        if ($quantity > 0) {
            $move_stmt = $conn->prepare("INSERT INTO stock_movements 
                    (product_id, user_id, movement_type, quantity, notes) 
                    VALUES (?, ?, 'stock_in', ?, 'Initial stock')");
            $move_stmt->bind_param("iii", $product_id, $_SESSION['user_id'], $quantity);
            $move_stmt->execute();
        }
        
        echo json_encode(['success' => true, 'message' => 'Product added successfully', 
                         'product_id' => $product_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product']);
    }
}

function updateProduct() {
    global $conn;
    
    $product_id = (int)($_POST['product_id'] ?? 0);
    $product_name = sanitizeInput($_POST['product_name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = sanitizeInput($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $cost = (float)($_POST['cost'] ?? 0);
    $reorder_level = (int)($_POST['reorder_level'] ?? 10);
    $supplier_name = sanitizeInput($_POST['supplier_name'] ?? '');
    $supplier_contact = sanitizeInput($_POST['supplier_contact'] ?? '');
    $status = sanitizeInput($_POST['status'] ?? 'active');
    
    if ($product_id <= 0 || empty($product_name) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        return;
    }
    
    // Handle image upload if provided
    $image_sql = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadProductImage($_FILES['image']);
        if ($image) {
            $image_sql = ", image = '$image'";
        }
    }
    
    $sql = "UPDATE products SET 
            product_name = ?, 
            category_id = ?, 
            description = ?, 
            price = ?, 
            cost = ?, 
            reorder_level = ?,
            supplier_name = ?,
            supplier_contact = ?,
            status = ?
            $image_sql
            WHERE product_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisddiissi", 
        $product_name, $category_id, $description, $price, $cost, 
        $reorder_level, $supplier_name, $supplier_contact, $status, $product_id
    );
    
    if ($stmt->execute()) {
        logActivity($_SESSION['user_id'], 'Updated product', 'Inventory', 
                   "Product ID: $product_id");
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update product']);
    }
}

function deleteProduct() {
    global $conn;
    
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    // Check if product has sales history
    $check = $conn->query("SELECT COUNT(*) as count FROM sales WHERE product_id = $product_id");
    $result = $check->fetch_assoc();
    
    if ($result['count'] > 0) {
        // Don't delete, just mark as discontinued
        $conn->query("UPDATE products SET status = 'discontinued' WHERE product_id = $product_id");
        $message = 'Product marked as discontinued (has sales history)';
    } else {
        // Safe to delete
        $conn->query("DELETE FROM products WHERE product_id = $product_id");
        $message = 'Product deleted successfully';
    }
    
    logActivity($_SESSION['user_id'], 'Deleted/Discontinued product', 'Inventory', 
               "Product ID: $product_id");
    
    echo json_encode(['success' => true, 'message' => $message]);
}

function recordStockMovement() {
    global $conn;
    
    $product_id = (int)($_POST['product_id'] ?? 0);
    $movement_type = sanitizeInput($_POST['movement_type'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    if ($product_id <= 0 || !in_array($movement_type, ['stock_in', 'stock_out', 'adjustment']) || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        return;
    }
    
    // Get current stock
    $result = $conn->query("SELECT quantity, product_name FROM products WHERE product_id = $product_id");
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        return;
    }
    
    $product = $result->fetch_assoc();
    $current_stock = $product['quantity'];
    
    // Calculate new stock
    if ($movement_type == 'stock_in' || $movement_type == 'adjustment') {
        $new_stock = $current_stock + $quantity;
    } else {
        $new_stock = $current_stock - $quantity;
        if ($new_stock < 0) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            return;
        }
    }
    
    // Update product stock
    $conn->query("UPDATE products SET quantity = $new_stock WHERE product_id = $product_id");
    
    // Record movement
    $stmt = $conn->prepare("INSERT INTO stock_movements 
            (product_id, user_id, movement_type, quantity, notes) 
            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $product_id, $_SESSION['user_id'], $movement_type, $quantity, $notes);
    $stmt->execute();
    
    // Log activity
    logActivity($_SESSION['user_id'], 'Stock movement recorded', 'Inventory', 
               "Product: {$product['product_name']}, Type: $movement_type, Qty: $quantity");
    
    // Check if low stock notification needed
    $reorder = $conn->query("SELECT reorder_level FROM products WHERE product_id = $product_id")->fetch_assoc();
    if ($new_stock <= $reorder['reorder_level']) {
        createNotification('low_stock', 
            "{$product['product_name']} is running low. Only $new_stock units remaining.", 
            null);
    }
    
    echo json_encode(['success' => true, 'message' => 'Stock movement recorded', 
                     'new_stock' => $new_stock]);
}

function getCategories() {
    global $conn;
    
    $result = $conn->query("SELECT * FROM categories ORDER BY category_name");
    $categories = [];
    
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    echo json_encode(['success' => true, 'categories' => $categories]);
}

function uploadProductImage($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return false;
    }
    
    $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
    $upload_path = UPLOAD_PATH . 'products/';
    
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_path . $new_filename)) {
        return $new_filename;
    }
    
    return false;
}
?>