<?php
// admin/users.php - User Management System
require_once '../config.php';
requireAdmin();

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_user':
            $username = sanitizeInput($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $full_name = sanitizeInput($_POST['full_name']);
            $email = sanitizeInput($_POST['email']);
            $role = sanitizeInput($_POST['role']);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $password, $full_name, $email, $role);
            
            if ($stmt->execute()) {
                $success = "User added successfully";
                logActivity($_SESSION['user_id'], 'Added new user', 'User Management', "Username: $username");
            } else {
                $error = "Failed to add user: " . $conn->error;
            }
            break;
            
        case 'update_user':
            $user_id = (int)$_POST['user_id'];
            $full_name = sanitizeInput($_POST['full_name']);
            $email = sanitizeInput($_POST['email']);
            $role = sanitizeInput($_POST['role']);
            $status = sanitizeInput($_POST['status']);
            
            $sql = "UPDATE users SET full_name = ?, email = ?, role = ?, status = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $full_name, $email, $role, $status, $user_id);
            
            if ($stmt->execute()) {
                $success = "User updated successfully";
                logActivity($_SESSION['user_id'], 'Updated user', 'User Management', "User ID: $user_id");
            }
            break;
            
        case 'reset_password':
            $user_id = (int)$_POST['user_id'];
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $new_password, $user_id);
            
            if ($stmt->execute()) {
                $success = "Password reset successfully";
                logActivity($_SESSION['user_id'], 'Reset user password', 'User Management', "User ID: $user_id");
            }
            break;
            
        case 'delete_user':
            $user_id = (int)$_POST['user_id'];
            
            // Check if user has sales/activity
            $check = $conn->query("SELECT COUNT(*) as count FROM sales WHERE user_id = $user_id");
            $result = $check->fetch_assoc();
            
            if ($result['count'] > 0) {
                // Deactivate instead of delete
                $conn->query("UPDATE users SET status = 'inactive' WHERE user_id = $user_id");
                $success = "User deactivated (has transaction history)";
            } else {
                $conn->query("DELETE FROM users WHERE user_id = $user_id");
                $success = "User deleted successfully";
            }
            
            logActivity($_SESSION['user_id'], 'Deleted/Deactivated user', 'User Management', "User ID: $user_id");
            break;
    }
}

// Fetch all users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = $conn->query($users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - InventraX</title>
    <!-- Include same styles as admin dashboard -->
</head>
<body>
    <!-- Include sidebar -->
    <div class="content-area">
        <h1>User Management</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <button onclick="openAddUserModal()">Add New User</button>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="badge badge-<?= $user['role'] == 'admin' ? 'primary' : 'secondary' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                            <?= ucfirst($user['status']) ?>
                        </span>
                    </td>
                    <td><?= $user['last_login'] ? formatDateTime($user['last_login']) : 'Never' ?></td>
                    <td>
                        <button onclick="editUser(<?= $user['user_id'] ?>)">Edit</button>
                        <button onclick="resetPassword(<?= $user['user_id'] ?>)">Reset Password</button>
                        <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                            <button onclick="deleteUser(<?= $user['user_id'] ?>)">Delete</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>