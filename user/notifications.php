<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - InventraX</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
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
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-gray);
        }

        .main-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-outline {
            background: white;
            border: 2px solid var(--border);
            color: var(--text);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .filter-tabs {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
        }

        .filter-tab {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            color: var(--text-light);
            transition: all 0.3s;
            border-radius: 8px;
        }

        .filter-tab.active {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }

        .notification-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .notification-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: flex;
            gap: 15px;
            transition: all 0.3s;
            cursor: pointer;
            border-left: 4px solid;
            position: relative;
        }

        .notification-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .notification-item.unread {
            background: rgba(99, 102, 241, 0.03);
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            top: 20px;
            right: 20px;
            width: 10px;
            height: 10px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
        }

        .notification-item.low_stock { border-left-color: var(--warning); }
        .notification-item.low_stock .notification-icon { background: var(--warning); }

        .notification-item.expiry { border-left-color: var(--danger); }
        .notification-item.expiry .notification-icon { background: var(--danger); }

        .notification-item.fast_moving { border-left-color: var(--success); }
        .notification-item.fast_moving .notification-icon { background: var(--success); }

        .notification-item.system { border-left-color: var(--info); }
        .notification-item.system .notification-icon { background: var(--info); }

        .notification-content {
            flex: 1;
        }

        .notification-content h3 {
            font-size: 16px;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .notification-content p {
            color: var(--text);
            font-size: 14px;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .notification-time {
            color: var(--text-light);
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .notification-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-light);
            font-size: 18px;
            padding: 5px;
            transition: color 0.3s;
        }

        .action-btn:hover {
            color: var(--primary);
        }

        .empty-state {
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--text-light);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h2 {
            color: var(--text);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-light);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <h1>
                <i class="fas fa-bell"></i>
                Notifications
            </h1>
            <div class="header-actions">
                <button class="btn btn-outline" onclick="markAllRead()">
                    <i class="fas fa-check-double"></i>
                    Mark all as read
                </button>
                <button class="btn btn-primary" onclick="clearAll()">
                    <i class="fas fa-trash"></i>
                    Clear all
                </button>
            </div>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterNotifications('all')">
                All
            </button>
            <button class="filter-tab" onclick="filterNotifications('unread')">
                Unread
            </button>
            <button class="filter-tab" onclick="filterNotifications('low_stock')">
                Low Stock
            </button>
            <button class="filter-tab" onclick="filterNotifications('expiry')">
                Expiring Soon
            </button>
            <button class="filter-tab" onclick="filterNotifications('system')">
                System
            </button>
        </div>

        <div class="notification-list" id="notificationList">
            <!-- Notification 1 - Low Stock -->
            <div class="notification-item low_stock unread" data-type="low_stock" data-id="1">
                <div class="notification-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="notification-content">
                    <h3>Low Stock Alert</h3>
                    <p><strong>Wireless Mouse Pro</strong> is running low. Only <strong>5 units</strong> remaining. Consider restocking soon.</p>
                    <div class="notification-time">
                        <i class="fas fa-clock"></i>
                        <span>2 minutes ago</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" onclick="markAsRead(1)" title="Mark as read">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="action-btn" onclick="deleteNotification(1)" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Notification 2 - Expiry -->
            <div class="notification-item expiry unread" data-type="expiry" data-id="2">
                <div class="notification-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="notification-content">
                    <h3>Product Expiring Soon</h3>
                    <p><strong>Premium Batteries Pack</strong> will expire in <strong>7 days</strong> (Jan 25, 2025). Please check inventory.</p>
                    <div class="notification-time">
                        <i class="fas fa-clock"></i>
                        <span>1 hour ago</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" onclick="markAsRead(2)">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="action-btn" onclick="deleteNotification(2)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Notification 3 - Fast Moving -->
            <div class="notification-item fast_moving" data-type="fast_moving" data-id="3">
                <div class="notification-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="notification-content">
                    <h3>Fast-Moving Product</h3>
                    <p><strong>Mechanical Keyboard RGB</strong> has sold <strong>45 units</strong> in the past 7 days. High demand detected!</p>
                    <div class="notification-time">
                        <i class="fas fa-clock"></i>
                        <span>3 hours ago</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" onclick="deleteNotification(3)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Notification 4 - System -->
            <div class="notification-item system" data-type="system" data-id="4">
                <div class="notification-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="notification-content">
                    <h3>System Update</h3>
                    <p>Database backup completed successfully. All data has been backed up to the server.</p>
                    <div class="notification-time">
                        <i class="fas fa-clock"></i>
                        <span>Yesterday</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" onclick="deleteNotification(4)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Notification 5 - Low Stock -->
            <div class="notification-item low_stock unread" data-type="low_stock" data-id="5">
                <div class="notification-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="notification-content">
                    <h3>Low Stock Alert</h3>
                    <p><strong>USB Cable Type-C</strong> stock is critically low. Only <strong>2 units</strong> left.</p>
                    <div class="notification-time">
                        <i class="fas fa-clock"></i>
                        <span>2 days ago</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" onclick="markAsRead(5)">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="action-btn" onclick="deleteNotification(5)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State (hidden by default) -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="fas fa-bell-slash"></i>
            <h2>No notifications</h2>
            <p>You're all caught up! Check back later for updates.</p>
        </div>
    </div>

    <script>
        function filterNotifications(filter) {
            const items = document.querySelectorAll('.notification-item');
            const tabs = document.querySelectorAll('.filter-tab');
            const list = document.getElementById('notificationList');
            const emptyState = document.getElementById('emptyState');
            
            // Update active tab
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
            
            let visibleCount = 0;
            
            items.forEach(item => {
                if (filter === 'all') {
                    item.style.display = 'flex';
                    visibleCount++;
                } else if (filter === 'unread') {
                    if (item.classList.contains('unread')) {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                } else {
                    if (item.dataset.type === filter) {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
            
            // Show/hide empty state
            if (visibleCount === 0) {
                list.style.display = 'none';
                emptyState.style.display = 'block';
            } else {
                list.style.display = 'flex';
                emptyState.style.display = 'none';
            }
        }

        function markAsRead(id) {
            const item = document.querySelector(`[data-id="${id}"]`);
            if (item) {
                item.classList.remove('unread');
                // Here you would send AJAX request to update database
                console.log('Marked notification', id, 'as read');
            }
        }

        function deleteNotification(id) {
            const item = document.querySelector(`[data-id="${id}"]`);
            if (item && confirm('Delete this notification?')) {
                item.style.animation = 'slideOut 0.3s';
                setTimeout(() => {
                    item.remove();
                    checkEmpty();
                }, 300);
                // Here you would send AJAX request to delete from database
                console.log('Deleted notification', id);
            }
        }

        function markAllRead() {
            const unreadItems = document.querySelectorAll('.notification-item.unread');
            unreadItems.forEach(item => {
                item.classList.remove('unread');
            });
            alert('All notifications marked as read');
        }

        function clearAll() {
            if (confirm('Clear all notifications?')) {
                const list = document.getElementById('notificationList');
                list.innerHTML = '';
                document.getElementById('emptyState').style.display = 'block';
                list.style.display = 'none';
            }
        }

        function checkEmpty() {
            const items = document.querySelectorAll('.notification-item');
            if (items.length === 0) {
                document.getElementById('notificationList').style.display = 'none';
                document.getElementById('emptyState').style.display = 'block';
            }
        }
    </script>
</body>
</html>