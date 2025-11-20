<?php
// admin/activity-logs.php - Activity Monitoring System
require_once '../config.php';
requireAdmin();

// Pagination
$page = (int)($_GET['page'] ?? 1);
$per_page = 50;
$offset = ($page - 1) * $per_page;

// Filters
$user_filter = (int)($_GET['user_id'] ?? 0);
$module_filter = sanitizeInput($_GET['module'] ?? '');
$date_filter = sanitizeInput($_GET['date'] ?? '');

// Build query
$where_conditions = [];
$params = [];
$types = '';

if ($user_filter > 0) {
    $where_conditions[] = "al.user_id = ?";
    $params[] = $user_filter;
    $types .= 'i';
}

if (!empty($module_filter)) {
    $where_conditions[] = "al.module = ?";
    $params[] = $module_filter;
    $types .= 's';
}

if (!empty($date_filter)) {
    $where_conditions[] = "DATE(al.created_at) = ?";
    $params[] = $date_filter;
    $types .= 's';
}

$where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM activity_logs al $where_sql";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Get activity logs
$sql = "SELECT al.*, u.full_name, u.username 
        FROM activity_logs al
        JOIN users u ON al.user_id = u.user_id
        $where_sql
        ORDER BY al.created_at DESC
        LIMIT $per_page OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result();

// Get unique modules for filter
$modules = $conn->query("SELECT DISTINCT module FROM activity_logs WHERE module IS NOT NULL ORDER BY module");

// Get all users for filter
$users = $conn->query("SELECT user_id, full_name FROM users ORDER BY full_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs - InventraX</title>
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
            color: var(--text);
        }

        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
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

        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .logs-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--light-gray);
        }

        .data-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 15px;
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

        .badge-primary {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .action-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-right: 10px;
        }

        .action-icon.login {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .action-icon.logout {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .action-icon.create {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }

        .action-icon.delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .pagination {
            padding: 20px;
            display: flex;
            justify-content: center;
            gap: 8px;
            border-top: 1px solid var(--border);
        }

        .page-link {
            padding: 8px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-link:hover {
            background: var(--light-gray);
            border-color: var(--primary);
        }

        .page-link.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--text-light);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <h1>
                <i class="fas fa-clock-rotate-left"></i>
                Activity Logs
            </h1>
            <button class="btn btn-outline" onclick="exportLogs()">
                <i class="fas fa-download"></i>
                Export CSV
            </button>
        </div>

        <!-- Statistics -->
        <div class="stats-cards">
            <div class="stat-card">
                <h3><?= number_format($total_records) ?></h3>
                <p>Total Activities</p>
            </div>
            <div class="stat-card">
                <h3><?= $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM activity_logs")->fetch_assoc()['count'] ?></h3>
                <p>Active Users</p>
            </div>
            <div class="stat-card">
                <h3><?= $conn->query("SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'] ?></h3>
                <p>Today's Activities</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label>User</label>
                <select class="form-control" name="user_id" onchange="applyFilters()">
                    <option value="">All Users</option>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <option value="<?= $user['user_id'] ?>" <?= $user_filter == $user['user_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['full_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Module</label>
                <select class="form-control" name="module" onchange="applyFilters()">
                    <option value="">All Modules</option>
                    <?php while ($module = $modules->fetch_assoc()): ?>
                        <option value="<?= $module['module'] ?>" <?= $module_filter == $module['module'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($module['module']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Date</label>
                <input type="date" class="form-control" name="date" value="<?= $date_filter ?>" onchange="applyFilters()">
            </div>

            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Clear
            </button>
        </div>

        <!-- Logs Table -->
        <div class="logs-container">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($logs->num_rows > 0): ?>
                            <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $log['log_id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($log['full_name']) ?></strong><br>
                                    <small style="color: var(--text-light);">@<?= htmlspecialchars($log['username']) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $action_lower = strtolower($log['action']);
                                    if (strpos($action_lower, 'login') !== false) {
                                        echo '<i class="action-icon login fas fa-sign-in-alt"></i>';
                                    } elseif (strpos($action_lower, 'logout') !== false) {
                                        echo '<i class="action-icon logout fas fa-sign-out-alt"></i>';
                                    } elseif (strpos($action_lower, 'add') !== false || strpos($action_lower, 'create') !== false) {
                                        echo '<i class="action-icon create fas fa-plus"></i>';
                                    } elseif (strpos($action_lower, 'delete') !== false) {
                                        echo '<i class="action-icon delete fas fa-trash"></i>';
                                    } else {
                                        echo '<i class="action-icon create fas fa-pen"></i>';
                                    }
                                    echo htmlspecialchars($log['action']);
                                    ?>
                                </td>
                                <td>
                                    <?php if ($log['module']): ?>
                                        <span class="badge badge-primary"><?= htmlspecialchars($log['module']) ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-light);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="max-width: 300px;">
                                    <?= $log['details'] ? htmlspecialchars($log['details']) : '-' ?>
                                </td>
                                <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                <td><?= formatDateTime($log['created_at']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-light);">
                                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i><br>
                                    No activity logs found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $user_filter ? '&user_id=' . $user_filter : '' ?><?= $module_filter ? '&module=' . $module_filter : '' ?><?= $date_filter ? '&date=' . $date_filter : '' ?>" 
                       class="page-link">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?= $i ?><?= $user_filter ? '&user_id=' . $user_filter : '' ?><?= $module_filter ? '&module=' . $module_filter : '' ?><?= $date_filter ? '&date=' . $date_filter : '' ?>" 
                       class="page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $user_filter ? '&user_id=' . $user_filter : '' ?><?= $module_filter ? '&module=' . $module_filter : '' ?><?= $date_filter ? '&date=' . $date_filter : '' ?>" 
                       class="page-link">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function applyFilters() {
            const user = document.querySelector('select[name="user_id"]').value;
            const module = document.querySelector('select[name="module"]').value;
            const date = document.querySelector('input[name="date"]').value;
            
            let url = '?page=1';
            if (user) url += '&user_id=' + user;
            if (module) url += '&module=' + module;
            if (date) url += '&date=' + date;
            
            window.location.href = url;
        }

        function clearFilters() {
            window.location.href = '?';
        }

        function exportLogs() {
            alert('Exporting activity logs to CSV...');
            // Implement CSV export
        }
    </script>
</body>
</html>