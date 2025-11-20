<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - InventraX</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
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
            --info: #06b6d4;
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

        .sidebar-header .badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 8px;
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

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .date-filter {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .date-filter select {
            padding: 8px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
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

        .content-area {
            padding: 30px;
        }

        /* Enhanced Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            font-family: 'Font Awesome 6 Free';
            font-size: 80px;
            font-weight: 900;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
        }

        .stat-content {
            flex: 1;
        }

        .stat-content h3 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            font-weight: 500;
        }

        .stat-trend.up {
            color: var(--success);
        }

        .stat-trend.down {
            color: var(--danger);
        }

        .stat-card.revenue { border-left-color: var(--success); }
        .stat-card.revenue .stat-icon { background: linear-gradient(135deg, var(--success), #059669); }

        .stat-card.sales { border-left-color: var(--primary); }
        .stat-card.sales .stat-icon { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }

        .stat-card.products { border-left-color: var(--info); }
        .stat-card.products .stat-icon { background: linear-gradient(135deg, var(--info), #0891b2); }

        .stat-card.low-stock { border-left-color: var(--warning); }
        .stat-card.low-stock .stat-icon { background: linear-gradient(135deg, var(--warning), #d97706); }

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
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

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-actions {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--light-gray);
        }

        .data-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            font-size: 13px;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .data-table tr:hover {
            background: var(--light-gray);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: var(--border);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--success));
            border-radius: 10px;
            transition: width 0.3s;
        }

        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .stats-grid {
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
                <i class="fas fa-shield-halved"></i>
                <h2>InventraX</h2>
                <div class="badge">ADMIN PANEL</div>
            </div>
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fas fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="analytics.php" class="nav-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Analytics</span>
                </a>
                <a href="products.php" class="nav-item">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <a href="categories.php" class="nav-item">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="users.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>User Management</span>
                </a>
                <a href="reports.php" class="nav-item">
                    <i class="fas fa-file-excel"></i>
                    <span>Reports</span>
                </a>
                <a href="activity-logs.php" class="nav-item">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span>Activity Logs</span>
                </a>
                <a href="settings.php" class="nav-item">
                    <i class="fas fa-gear"></i>
                    <span>Settings</span>
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
                <h1><i class="fas fa-chart-line"></i> Admin Dashboard</h1>
                <div class="header-actions">
                    <div class="date-filter">
                        <select id="periodFilter" onchange="updateDashboard()">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export Report
                    </button>
                </div>
            </header>

            <div class="content-area">
                <!-- Enhanced Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card revenue">
                        <div class="stat-icon">
                            <i class="fas fa-peso-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3>₱458,920</h3>
                            <p>Total Revenue</p>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>12.5% from last month</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card sales">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3>2,847</h3>
                            <p>Total Sales</p>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>8.2% from last month</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card products">
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="stat-content">
                            <h3>1,248</h3>
                            <p>Active Products</p>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>24 new this month</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card low-stock">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>12</h3>
                            <p>Low Stock Items</p>
                            <div class="stat-trend down">
                                <i class="fas fa-arrow-down"></i>
                                <span>Needs attention</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Sales Trend</h3>
                            <div class="card-actions">
                                <button class="btn btn-sm">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                        <canvas id="salesTrendChart"></canvas>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie"></i> Sales by Category</h3>
                        </div>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Top Products & Recent Activity -->
                <div class="charts-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-fire"></i> Top Selling Products</h3>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Wireless Mouse Pro</strong><br>
                                        <small>SKU: WMP-001</small>
                                    </td>
                                    <td>342</td>
                                    <td>₱427,500</td>
                                    <td>
                                        <span class="badge badge-success">145 units</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Mechanical Keyboard RGB</strong><br>
                                        <small>SKU: MKB-002</small>
                                    </td>
                                    <td>198</td>
                                    <td>₱683,100</td>
                                    <td>
                                        <span class="badge badge-warning">8 units</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Premium Ballpen Set</strong><br>
                                        <small>SKU: PEN-003</small>
                                    </td>
                                    <td>856</td>
                                    <td>₱243,960</td>
                                    <td>
                                        <span class="badge badge-success">320 units</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Office Chair Pro</strong><br>
                                        <small>SKU: CHR-004</small>
                                    </td>
                                    <td>76</td>
                                    <td>₱680,200</td>
                                    <td>
                                        <span class="badge badge-success">24 units</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-clock"></i> Recent Activity</h3>
                        </div>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                                <strong>John Doe</strong> completed a sale
                                <br><small style="color: var(--text-light);">2 minutes ago</small>
                            </div>
                            <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                                <strong>Jane Smith</strong> added new product
                                <br><small style="color: var(--text-light);">15 minutes ago</small>
                            </div>
                            <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                                <strong>Admin</strong> updated system settings
                                <br><small style="color: var(--text-light);">1 hour ago</small>
                            </div>
                            <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                                <strong>Mike Johnson</strong> processed return
                                <br><small style="color: var(--text-light);">2 hours ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Revenue',
                    data: [95000, 112000, 128000, 123000],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value.toLocaleString()
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Electronics', 'Office Supplies', 'Furniture', 'Others'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: [
                        '#6366f1',
                        '#10b981',
                        '#f59e0b',
                        '#64748b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        function updateDashboard() {
            const period = document.getElementById('periodFilter').value;
            console.log('Updating dashboard for period:', period);
            // Fetch new data based on period
        }

        function exportReport() {
            alert('Exporting report...');
        }
    </script>
</body>
</html>