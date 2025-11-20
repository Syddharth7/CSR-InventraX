<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - InventraX</title>
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

        /* Sidebar (reused from dashboard) */
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
            display: block;
            padding: 14px 20px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
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

        .header-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .content-area {
            padding: 30px;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .filter-group {
            display: flex;
            gap: 10px;
        }

        .filter-select {
            padding: 10px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            background: white;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }

        .product-body {
            padding: 20px;
        }

        .product-category {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .product-sku {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 12px;
        }

        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .product-stock {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .stock-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 12px;
        }

        .stock-badge.high {
            background: #d1fae5;
            color: #065f46;
        }

        .stock-badge.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .stock-badge.low {
            background: #fee2e2;
            color: #991b1b;
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 13px;
            flex: 1;
        }

        .btn-outline {
            background: white;
            border: 2px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 12px;
            overflow: hidden;
            animation: slideDown 0.3s;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 20px;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .close-btn:hover {
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
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

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .file-upload {
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .file-upload i {
            font-size: 48px;
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .modal-footer {
            padding: 20px 25px;
            background: var(--light-gray);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
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
                <a href="inventory.php" class="nav-item active">
                    <i class="fas fa-box"></i>
                    <span>Inventory</span>
                </a>
                <a href="sales.php" class="nav-item">
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
                <h1><i class="fas fa-box"></i> Inventory Management</h1>
                <div class="header-actions">
                    <button class="btn btn-success" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                    <button class="btn btn-primary" onclick="openStockModal()">
                        <i class="fas fa-arrows-rotate"></i> Stock Movement
                    </button>
                </div>
            </header>

            <div class="content-area">
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
                    </div>
                    <div class="filter-group">
                        <select class="filter-select" id="categoryFilter" onchange="filterProducts()">
                            <option value="">All Categories</option>
                            <option value="electronics">Electronics</option>
                            <option value="office">Office Supplies</option>
                            <option value="furniture">Furniture</option>
                        </select>
                        <select class="filter-select" id="stockFilter" onchange="filterProducts()">
                            <option value="">All Stock</option>
                            <option value="high">In Stock</option>
                            <option value="low">Low Stock</option>
                            <option value="out">Out of Stock</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="product-grid" id="productGrid">
                    <!-- Product Card 1 -->
                    <div class="product-card" data-category="electronics" data-stock="high">
                        <div class="product-image">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="product-body">
                            <span class="product-category">Electronics</span>
                            <h3 class="product-name">Wireless Mouse Pro</h3>
                            <p class="product-sku">SKU: WMP-001</p>
                            <div class="product-price">₱1,250.00</div>
                            <div class="product-stock">
                                <span>Stock:</span>
                                <span class="stock-badge high">145 units</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-outline btn-sm" onclick="editProduct(1)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="viewProduct(1)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="product-card" data-category="electronics" data-stock="low">
                        <div class="product-image">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <div class="product-body">
                            <span class="product-category">Electronics</span>
                            <h3 class="product-name">Mechanical Keyboard RGB</h3>
                            <p class="product-sku">SKU: MKB-002</p>
                            <div class="product-price">₱3,450.00</div>
                            <div class="product-stock">
                                <span>Stock:</span>
                                <span class="stock-badge low">8 units</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-outline btn-sm" onclick="editProduct(2)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="viewProduct(2)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 3 -->
                    <div class="product-card" data-category="office" data-stock="high">
                        <div class="product-image">
                            <i class="fas fa-pen"></i>
                        </div>
                        <div class="product-body">
                            <span class="product-category">Office Supplies</span>
                            <h3 class="product-name">Premium Ballpen Set</h3>
                            <p class="product-sku">SKU: PEN-003</p>
                            <div class="product-price">₱285.00</div>
                            <div class="product-stock">
                                <span>Stock:</span>
                                <span class="stock-badge high">320 units</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-outline btn-sm" onclick="editProduct(3)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="viewProduct(3)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 4 -->
                    <div class="product-card" data-category="furniture" data-stock="medium">
                        <div class="product-image">
                            <i class="fas fa-chair"></i>
                        </div>
                        <div class="product-body">
                            <span class="product-category">Furniture</span>
                            <h3 class="product-name">Ergonomic Office Chair</h3>
                            <p class="product-sku">SKU: CHR-004</p>
                            <div class="product-price">₱8,950.00</div>
                            <div class="product-stock">
                                <span>Stock:</span>
                                <span class="stock-badge medium">24 units</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-outline btn-sm" onclick="editProduct(4)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="viewProduct(4)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-box"></i> Add New Product</h2>
                <button class="close-btn" onclick="closeModal('productModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" class="form-control" placeholder="Enter product name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>SKU *</label>
                            <input type="text" class="form-control" placeholder="e.g., PRD-001" required>
                        </div>
                        <div class="form-group">
                            <label>Category *</label>
                            <select class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="electronics">Electronics</option>
                                <option value="office">Office Supplies</option>
                                <option value="furniture">Furniture</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" placeholder="Product description..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Price (₱) *</label>
                            <input type="number" class="form-control" placeholder="0.00" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Cost (₱)</label>
                            <input type="number" class="form-control" placeholder="0.00" step="0.01">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Initial Stock *</label>
                            <input type="number" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Reorder Level</label>
                            <input type="number" class="form-control" placeholder="10" value="10">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <div class="file-upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <input type="file" hidden accept="image/*">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('productModal')">Cancel</button>
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Product
                </button>
            </div>
        </div>
    </div>

    <!-- Stock Movement Modal -->
    <div class="modal" id="stockModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-arrows-rotate"></i> Stock Movement</h2>
                <button class="close-btn" onclick="closeModal('stockModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="stockForm">
                    <div class="form-group">
                        <label>Product *</label>
                        <select class="form-control" required>
                            <option value="">Select Product</option>
                            <option value="1">Wireless Mouse Pro</option>
                            <option value="2">Mechanical Keyboard RGB</option>
                            <option value="3">Premium Ballpen Set</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Movement Type *</label>
                        <select class="form-control" required>
                            <option value="stock_in">Stock In</option>
                            <option value="stock_out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" class="form-control" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" placeholder="Reason for movement..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('stockModal')">Cancel</button>
                <button class="btn btn-success">
                    <i class="fas fa-check"></i> Submit
                </button>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('productModal').style.display = 'block';
        }

        function openStockModal() {
            document.getElementById('stockModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function filterProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const stockFilter = document.getElementById('stockFilter').value;
            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                const name = card.querySelector('.product-name').textContent.toLowerCase();
                const category = card.getAttribute('data-category');
                const stock = card.getAttribute('data-stock');

                const matchSearch = name.includes(searchTerm);
                const matchCategory = !categoryFilter || category === categoryFilter;
                const matchStock = !stockFilter || stock === stockFilter;

                if (matchSearch && matchCategory && matchStock) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function editProduct(id) {
            alert('Edit product ' + id);
        }

        function viewProduct(id) {
            alert('View product details ' + id);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>