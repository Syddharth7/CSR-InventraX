<?php
// login_process.php - Handle login authentication
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        redirect('login.php?error=1');
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, username, password, full_name, email, role, status FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if account is active
        if ($user['status'] !== 'active') {
            redirect('login.php?error=1');
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Update last login
            $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $update_stmt->bind_param("i", $user['user_id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Log activity
            logActivity($user['user_id'], 'User logged in', 'Authentication', 'Login from ' . $_SERVER['REMOTE_ADDR']);

            // Set remember me cookie if checked
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), '/');
            }

            // Redirect based on role - FIXED PATHS
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            } else {
                header("Location: user/dashboard.php");
                exit();
            }
        } else {
            redirect('login.php?error=1');
        }
    } else {
        redirect('login.php?error=1');
    }

    $stmt->close();
} else {
    redirect('login.php');
}
?>