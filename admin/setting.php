<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - InventraX</title>
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
            --bg-color: #f8fafc;
            --card-bg: white;
        }

        /* Dark Mode Variables */
        [data-theme="dark"] {
            --dark: #f1f5f9;
            --light-gray: #1e293b;
            --border: #334155;
            --text: #e2e8f0;
            --text-light: #94a3b8;
            --bg-color: #0f172a;
            --card-bg: #1e293b;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-color);
            color: var(--text);
            transition: background 0.3s, color 0.3s;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .header p {
            color: var(--text-light);
        }

        .settings-grid {
            display: grid;
            gap: 20px;
        }

        .settings-section {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
        }

        .section-header i {
            font-size: 24px;
            color: var(--primary);
        }

        .section-header h2 {
            font-size: 20px;
            color: var(--text);
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .setting-info h3 {
            font-size: 16px;
            color: var(--text);
            margin-bottom: 5px;
        }

        .setting-info p {
            font-size: 14px;
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: var(--card-bg);
            color: var(--text);
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--border);
            border-radius: 30px;
            transition: 0.3s;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        input:checked + .toggle-slider {
            background: var(--primary);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .btn {
            padding: 12px 24px;
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
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid var(--warning);
        }

        .theme-preview {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .theme-option {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .theme-option:hover {
            transform: scale(1.05);
        }

        .theme-option.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .theme-light {
            background: linear-gradient(to bottom, #6366f1 30%, #f8fafc 30%);
        }

        .theme-dark {
            background: linear-gradient(to bottom, #6366f1 30%, #1e293b 30%);
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <h1>
                <i class="fas fa-cog"></i>
                System Settings
            </h1>
            <p>Manage your InventraX system configuration and preferences</p>
        </div>

        <div class="settings-grid">
            <!-- Appearance Settings -->
            <div class="settings-section">
                <div class="section-header">
                    <i class="fas fa-palette"></i>
                    <h2>Appearance</h2>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Dark Mode</h3>
                        <p>Enable dark theme for better visibility in low light</p>
                        <div class="theme-preview">
                            <div class="theme-option theme-light active" onclick="setTheme('light')">
                                <small style="padding: 5px; text-align: center; color: white; font-weight: 600;">Light</small>
                            </div>
                            <div class="theme-option theme-dark" onclick="setTheme('dark')">
                                <small style="padding: 5px; text-align: center; color: white; font-weight: 600;">Dark</small>
                            </div>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode()">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Shop Information -->
            <div class="settings-section">
                <div class="section-header">
                    <i class="fas fa-store"></i>
                    <h2>Shop Information</h2>
                </div>

                <form id="shopInfoForm">
                    <div class="form-group">
                        <label>Shop Name</label>
                        <input type="text" class="form-control" value="InventraX Store" placeholder="Enter shop name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="info@inventrax.com" placeholder="shop@email.com">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" value="+63 912 345 6789" placeholder="+63 XXX XXX XXXX">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" rows="3" placeholder="Enter shop address">123 Business St., Cebu City, Philippines</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </form>
            </div>

            <!-- Inventory Settings -->
            <div class="settings-section">
                <div class="section-header">
                    <i class="fas fa-boxes"></i>
                    <h2>Inventory Settings</h2>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Low Stock Threshold</h3>
                        <p>Default minimum quantity before low stock alert</p>
                    </div>
                    <input type="number" class="form-control" style="width: 100px;" value="10" min="1">
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Auto-Reorder</h3>
                        <p>Automatically create purchase orders for low stock items</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Expiry Notifications</h3>
                        <p>Alert when products are nearing expiry date</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Settings
                </button>
            </div>

            <!-- Notifications Settings -->
            <div class="settings-section">
                <div class="section-header">
                    <i class="fas fa-bell"></i>
                    <h2>Notifications</h2>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Email Notifications</h3>
                        <p>Receive alerts via email</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Low Stock Alerts</h3>
                        <p>Get notified when products are low in stock</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Daily Sales Report</h3>
                        <p>Receive daily summary of sales and revenue</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Backup & Security -->
            <div class="settings-section">
                <div class="section-header">
                    <i class="fas fa-shield-halved"></i>
                    <h2>Backup & Security</h2>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Last backup: Today at 3:00 AM</span>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Automatic Backup</h3>
                        <p>Daily automatic database backup</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-success" onclick="backupDatabase()">
                        <i class="fas fa-download"></i>
                        Backup Now
                    </button>
                    <button class="btn btn-primary" onclick="openRestoreModal()">
                        <i class="fas fa-upload"></i>
                        Restore Backup
                    </button>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="settings-section">
                <div class="section-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h2>Danger Zone</h2>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>These actions are irreversible. Use with caution!</span>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-danger" onclick="clearLogs()">
                        <i class="fas fa-trash"></i>
                        Clear Activity Logs
                    </button>
                    <button class="btn btn-danger" onclick="resetSystem()">
                        <i class="fas fa-rotate"></i>
                        Reset to Default
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            const isDark = document.getElementById('darkModeToggle').checked;
            setTheme(isDark ? 'dark' : 'light');
        }

        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            document.getElementById('darkModeToggle').checked = (theme === 'dark');
            
            // Update theme previews
            document.querySelectorAll('.theme-option').forEach(option => {
                option.classList.remove('active');
            });
            
            if (theme === 'dark') {
                document.querySelector('.theme-dark').classList.add('active');
            } else {
                document.querySelector('.theme-light').classList.add('active');
            }
            
            // Save preference
            localStorage.setItem('theme', theme);
            console.log('Theme set to:', theme);
        }

        // Load saved theme
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });

        // Shop Info Form
        document.getElementById('shopInfoForm').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Shop information updated successfully!');
        });

        // Backup & Restore
        function backupDatabase() {
            if (confirm('Create a backup of the database now?')) {
                alert('Backup created successfully! Download will start shortly.');
                // Here you would trigger actual backup
                console.log('Backup database...');
            }
        }

        function openRestoreModal() {
            alert('Restore functionality - Upload backup file');
        }

        // Danger Zone
        function clearLogs() {
            if (confirm('Are you sure you want to clear all activity logs? This cannot be undone!')) {
                alert('Activity logs cleared');
            }
        }

        function resetSystem() {
            const confirmed = confirm('WARNING: This will reset all settings to default. Continue?');
            if (confirmed) {
                const doubleCheck = confirm('Are you ABSOLUTELY sure? This action cannot be undone!');
                if (doubleCheck) {
                    alert('System reset to defaults');
                }
            }
        }
    </script>
</body>
</html>