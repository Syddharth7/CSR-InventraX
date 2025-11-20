<?php
// register_process.php - Handle user registration
require_once 'config.php';

// Check if registration is allowed (you can add a setting for this)
$registration_enabled = true; // Set to false to disable public registration

if (!$registration_enabled) {
    redirect('login.php?error=' . urlencode('Registration is currently disabled'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    $errors = [];

    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }

    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 4) {
        $errors[] = 'Username must be at least 4 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    // Check if username already exists
    if (empty($errors)) {
        $check_username = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check_username->bind_param("s", $username);
        $check_username->execute();
        if ($check_username->get_result()->num_rows > 0) {
            $errors[] = 'Username already taken';
        }
        $check_username->close();
    }

    // Check if email already exists
    if (empty($errors)) {
        $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            $errors[] = 'Email already registered';
        }
        $check_email->close();
    }

    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $error_message = implode('. ', $errors);
        redirect('register.php?error=' . urlencode($error_message));
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user (default role is 'staff')
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role, status) 
                           VALUES (?, ?, ?, ?, 'staff', 'active')");
    $stmt->bind_param("ssss", $username, $hashed_password, $full_name, $email);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        
        // Log registration activity
        logActivity($user_id, 'User registered', 'Authentication', 'New user registration: ' . $username);
        
        // Auto-login the user after successful registration
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'staff';
        $_SESSION['last_activity'] = time();
        
        // Redirect to user dashboard
        header("Location: user/dashboard.php");
        exit();
        
        // OR redirect to login page with success message
        // redirect('login.php?success=' . urlencode('Account created successfully! Please login.'));
    } else {
        redirect('register.php?error=' . urlencode('Registration failed. Please try again.'));
    }

    $stmt->close();
} else {
    redirect('register.php');
}
?>