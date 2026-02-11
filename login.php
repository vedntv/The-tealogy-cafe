<?php
// login.php: Authenticate user by email and password
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'tealogy_login');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role_id = isset($_POST['role_id']) && $_POST['role_id'] !== '' ? intval($_POST['role_id']) : null;

    // Join with roles table to get role name and set it in session
    $stmt = $conn->prepare('SELECT u.id, u.username, u.password, u.role_id, r.role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $user_role_id, $role_name);
        $stmt->fetch();
        
        // Check if role filter matches (if user selected a specific role)
        if ($role_id !== null && $user_role_id != $role_id) {
            $error = 'Role does not match user account.';
        } else if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            // If role_name is empty, default to 'sales_staff'
            $_SESSION['role'] = ($role_name !== null && $role_name !== '') ? $role_name : 'sales_staff';
            header('Location: index.html'); // Redirect to homepage or dashboard
            exit();
        } else {
            $error = 'Invalid password.';
        }
    } else {
        $error = 'No user found with that email.';
    }
    $stmt->close();
}
$conn->close();
?>
<!-- Simple login form (for use in login.html) -->
<!--
<form action="login.php" method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
-->
