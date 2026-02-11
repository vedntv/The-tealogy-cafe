<?php
// create_root.php
// Run this once (open in browser) to create a default root/admin user in the `tealogy_login` database.
// IMPORTANT: Delete this file after use.

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "tealogy_login";

try {
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }

    // Ensure roles exist
    $res = $conn->query("SELECT id FROM roles WHERE role_name = 'root' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $root_role_id = $row['id'];
    } else {
        // Create roles if missing
        $conn->query("INSERT INTO roles (role_name) VALUES ('root'), ('sales_staff')");
        $root_role_id = $conn->insert_id; // id of first inserted (root)
    }

    // Check if a root user already exists
    $adminEmail = 'root@tealogy.local';
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $adminEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Root user already exists.\n";
    } else {
        // Create root user with a default password (change after creation)
        $defaultPass = 'admin123';
        if (isset($_GET['pass']) && trim($_GET['pass']) !== '') {
            $defaultPass = $_GET['pass'];
        }
        $hash = password_hash($defaultPass, PASSWORD_DEFAULT);
        $username = 'root';
        $email = $adminEmail;

        $ins = $conn->prepare('INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)');
        $ins->bind_param('sssi', $username, $email, $hash, $root_role_id);
        if ($ins->execute()) {
            echo "Root user created. Email: $email Password: $defaultPass\n";
            echo "PLEASE DELETE create_root.php NOW for security.";
        } else {
            echo "Failed to create root user: " . $ins->error;
        }
    }
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
