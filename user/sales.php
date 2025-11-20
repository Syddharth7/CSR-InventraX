<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - InventraX</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --light-gray: #f8fafc;
            --border: #e2e8f0;
            --text: #334155;
            --text-light: #64748b;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-gray);
            color: var(--text);
        }

        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            text-align: center;
        }

        .sidebar-header i {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .sidebar-header h2 {
            font-size: 22px;
            font-weight: 600;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: var(--light-gray);
            border-left-color: var(--primary);
            padding-left: 25px;
        }

        .nav-item.active {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
            font-weight: 600;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
        }

        .header {
            background: white;
            box-shadow: var(--shadow);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header h1 {
            font-size: 24px;
            color: var(--dark);
        }

        .content-area {
            padding: 30px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
        }

        .card-header h2 {
            font-size: 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-product {
            position: relative;
            margin-bottom: 20px;
        }

        .search-product input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .search-product input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .search-product i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .product-list {
            display: grid;
            gap: 12px;
            max-height: 500px;
            overflow-y: auto;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .product-item:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
            transform: translateX(5px);
        }

        .product-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .product-info {
            flex: 1;
        }

        .product-info h4 {
            font-size: 15px;
            margin-bottom: 4px;
            color: var(--dark);
        }

        .product-info p {
            font-size: 13px;
            color: var(--text-light);
        }

        .product-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
        }

        .product-stock {
            font-size: 12px;
            color: var(--success);
            margin-top: 4px;
        }

        .cart-section {
            position: sticky;
            top: 90px;
            height: fit-content;
        }

        .cart-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--light-gray);
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h4 {
            font-size: 14px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .cart-item-price {
            font-size: 13px;
            color: var(--text-light);
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            border-radius: 8px;
            padding: 4px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: var(--primary);
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .qty-btn:hover {
            background: var(--primary-dark);
        }

        .qty-display {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        .remove-btn {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 18px;
            transition: transform 0.3s;
        }

        .remove-btn:hover {
            transform: scale(1.2);
        }

        .cart-summary {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            padding-top: 12px;
            border-top: 2px solid var(--border);
            margin-top: 12px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-success:disabled {
            background: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-light);
        }

        .empty-cart i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .success-message {
            background: #d1fae5;
            border-left: 4px solid var(--success);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 1024px) {
            .content-area {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-boxes-stacked"></i>
                <h2>InventraX</h2>
            </div>
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-item">
                    <i class="fas fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="inventory.php" class="nav-item">
                    <i class="fas fa-box"></i>
                    <span>Inventory</span>
                </a>
                <a href="sales.php" class="nav-item active">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Sales</span>
                </a>
                <a href="reports.php" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Reports</span>
                </a>
                <a href="notifications.php" class="nav-item">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="profile.php" class="nav-item">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="../logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1><i class="fas fa-shopping-cart"></i> Sales Transaction</h1>
            </header>

            <div class="content-area">
                <!-- Product Selection -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-box"></i> Select Products</h2>
                    </div>

                    <div class="search-product">
                        <i class="fas fa-search"></i>
                        <input type="text" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()">
                    </div>

                    <div class="product-list" id="productList">
                        <!-- Product 1 -->
                        <div class="product-item" onclick="addToCart(1, 'Wireless Mouse Pro', 1250, 145)">
                            <div class="product-icon">
                                <i class="fas fa-mouse"></i>
                            </div>
                            <div class="product-info">
                                <h4>Wireless Mouse Pro</h4>
                                <p>SKU: WMP-001</p>
                                <div class="product-stock"><i class="fas fa-check-circle"></i> 145 in stock</div>
                            </div>
                            <div class="product-price">₱1,250.00</div>
                        </div>

                        <!-- Product 2 -->
                        <div class="product-item" onclick="addToCart(2, 'Mechanical Keyboard RGB', 3450, 8)">
                            <div class="product-icon">
                                <i class="fas fa-keyboard"></i>
                            </div>
                            <div class="product-info">
                                <h4>Mechanical Keyboard RGB</h4>
                                <p>SKU: MKB-002</p>
                                <div class="product-stock" style="color: var(--warning)">
                                    <i class="fas fa-exclamation-triangle"></i> 8 in stock
                                </div>
                            </div>
                            <div class="product-price">₱3,450.00</div>
                        </div>

                        <!-- Product 3 -->
                        <div class="product-item" onclick="addToCart(3, 'Premium Ballpen Set', 285, 320)">
                            <div class="product-icon">
                                <i class="fas fa-pen"></i>
                            </div>
                            <div class="product-info">
                                <h4>Premium Ballpen Set</h4>
                                <p>SKU: PEN-003</p>
                                <div class="product-stock"><i class="fas fa-check-circle"></i> 320 in stock</div>
                            </div>
                            <div class="product-price">₱285.00</div>
                        </div>

                        <!-- Product 4 -->
                        <div class="product-item" onclick="addToCart(4, 'Ergonomic Office Chair', 8950, 24)">
                            <div class="product-icon">
                                <i class="fas fa-chair"></i>
                            </div>
                            <div class="product-info">
                                <h4>Ergonomic Office Chair</h4>
                                <p>SKU: CHR-004</p>
                                <div class="product-stock"><i class="fas fa-check-circle"></i> 24 in stock</div>
                            </div>
                            <div class="product-price">₱8,950.00</div>
                        </div>
                    </div>
                </div>

                <!-- Cart Section -->
                <div class="cart-section">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-cart-shopping"></i> Cart</h2>
                        </div>

                        <div class="success-message" id="successMessage">
                            <i class="fas fa-check-circle"></i>
                            <strong>Sale completed successfully!</strong>
                        </div>

                        <div class="cart-items" id="cartItems">
                            <div class="empty-cart">
                                <i class="fas fa-cart-shopping"></i>
                                <p>No items in cart</p>
                            </div>
                        </div>

                        <div class="cart-summary" id="cartSummary" style="display: none;">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal">₱0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax (0%):</span>
                                <span id="tax">₱0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="total">₱0.00</span>
                            </div>
                        </div>

                        <div id="checkoutForm" style="display: none;">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select class="form-control" id="paymentMethod">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="online">Online Payment</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Customer Name (Optional)</label>
                                <input type="text" class="form-control" id="customerName" placeholder="Enter customer name">
                            </div>

                            <button class="btn btn-success" onclick="completeSale()">
                                <i class="fas fa-check"></i>
                                Complete Sale
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let cart = [];

        function addToCart(id, name, price, stock) {
            const existing = cart.find(item => item.id === id);
            
            if (existing) {
                if (existing.quantity < stock) {
                    existing.quantity++;
                } else {
                    alert('Not enough stock available!');
                    return;
                }
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    stock: stock
                });
            }
            
            updateCart();
        }

        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            const cartSummary = document.getElementById('cartSummary');
            const checkoutForm = document.getElementById('checkoutForm');
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<div class="empty-cart"><i class="fas fa-cart-shopping"></i><p>No items in cart</p></div>';
                cartSummary.style.display = 'none';
                checkoutForm.style.display = 'none';
                return;
            }
            
            cartSummary.style.display = 'block';
            checkoutForm.style.display = 'block';
            
            let html = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                html += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4>${item.name}</h4>
                            <div class="cart-item-price">₱${item.price.toFixed(2)} × ${item.quantity}</div>
                        </div>
                        <div class="quantity-control">
                            <button class="qty-btn" onclick="decreaseQty(${item.id})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="qty-display">${item.quantity}</div>
                            <button class="qty-btn" onclick="increaseQty(${item.id})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <button class="remove-btn" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });
            
            cartItems.innerHTML = html;
            
            const tax = 0; // No tax for now
            const total = subtotal + tax;
            
            document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
            document.getElementById('tax').textContent = '₱' + tax.toFixed(2);
            document.getElementById('total').textContent = '₱' + total.toFixed(2);
        }

        function increaseQty(id) {
            const item = cart.find(i => i.id === id);
            if (item && item.quantity < item.stock) {
                item.quantity++;
                updateCart();
            } else {
                alert('Not enough stock available!');
            }
        }

        function decreaseQty(id) {
            const item = cart.find(i => i.id === id);
            if (item && item.quantity > 1) {
                item.quantity--;
                updateCart();
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCart();
        }

        function completeSale() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }
            
            const paymentMethod = document.getElementById('paymentMethod').value;
            const customerName = document.getElementById('customerName').value;
            
            // Here you would send data to backend
            console.log('Sale Data:', {
                items: cart,
                payment_method: paymentMethod,
                customer_name: customerName
            });
            
            // Show success message
            document.getElementById('successMessage').style.display = 'block';
            setTimeout(() => {
                document.getElementById('successMessage').style.display = 'none';
            }, 3000);
            
            // Clear cart
            cart = [];
            updateCart();
            document.getElementById('customerName').value = '';
        }

        function filterProducts() {
            const search = document.getElementById('productSearch').value.toLowerCase();
            const items = document.querySelectorAll('.product-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(search)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>