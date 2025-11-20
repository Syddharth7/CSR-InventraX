<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - InventraX</title>
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
            --dark: #1e293b;
            --light-gray: #f8fafc;
            --border: #e2e8f0;
            --text: #334155;
            --text-light: #64748b;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --sidebar-width: 260px;
            --header-height: 70px;
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

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            transition: all 0.3s;
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
            font-size: 15px;
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

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: white;
            height: var(--header-height);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left h1 {
            font-size: 24px;
            color: var(--dark);
            font-weight: 600;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-btn, .profile-btn {
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
            font-size: 20px;
            color: var(--text);
            transition: color 0.3s;
        }

        .notification-btn:hover, .profile-btn:hover {
            color: var(--primary);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--success));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .profile-name {
            font-weight: 500;
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }

        .stat-content h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: var(--text-light);
            font-size: 14px;
        }

        .stat-card.primary { border-left-color: var(--primary); }
        .stat-card.primary .stat-icon { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }

        .stat-card.success { border-left-color: var(--success); }
        .stat-card.success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }

        .stat-card.warning { border-left-color: var(--warning); }
        .stat-card.warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .stat-card.danger { border-left-color: var(--danger); }
        .stat-card.danger .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }

        /* Charts & Tables */
        .dashboard-grid {
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
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
        }

        .alert-box {
            background: #fef3c7;
            border-left: 4px solid var(--warning);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-box i {
            color: var(--warning);
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-grid {
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
                <a href="dashboard.php" class="nav-item active">
                    <i class="fas fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="inventory.php" class="nav-item">
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
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="profile-info">
                        <div class="profile-avatar">SA</div>
                        <span class="profile-name">Staff Account</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content-area">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="stat-content">
                            <h3>1,248</h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-content">
                            <h3>₱45,820</h3>
                            <p>Today's Sales</p>
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>12</h3>
                            <p>Low Stock Items</p>
                        </div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>342</h3>
                            <p>Orders Today</p>
                        </div>
                    </div>
                </div>

                <!-- Charts & Alerts -->
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3>Sales Overview</h3>
                        </div>
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Low Stock Alerts</h3>
                        </div>
                        <div class="alert-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>USB Cable Type-C</strong><br>
                                <small>Only 5 units left</small>
                            </div>
                        </div>
                        <div class="alert-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Wireless Mouse</strong><br>
                                <small>Only 3 units left</small>
                            </div>
                        </div>
                        <div class="alert-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Office Chair Pro</strong><br>
                                <small>Only 2 units left</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Sales',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>