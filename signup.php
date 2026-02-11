<?php
// signup.php - receive signup form and create a new user in tealogy_login.users
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$phone = trim($_POST['phone'] ?? '');
$age = isset($_POST['age']) && $_POST['age'] !== '' ? intval($_POST['age']) : null;
$sex = trim($_POST['sex'] ?? '');
$state = trim($_POST['state'] ?? '');
$address = trim($_POST['address'] ?? '');
$role_id = intval($_POST['role_id'] ?? 2); // default to sales_staff

// basic validation
if ($username === '' || $email === '' || $password === '') {
    header('Location: login.html?signup=error&msg=missing');
    exit;
}

// connect
$conn = new mysqli('localhost', 'root', '', 'tealogy_login');
if ($conn->connect_error) {
    header('Location: login.html?signup=error&msg=db');
    exit;
}

// check for existing email or username
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
$stmt->bind_param('ss', $email, $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $conn->close();
    header('Location: login.html?signup=error&msg=exists');
    exit;
}
$stmt->close();

// hash password and insert
$hash = password_hash($password, PASSWORD_DEFAULT);
$ins = $conn->prepare('INSERT INTO users (username, email, password, phone, age, sex, state, address, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$ins->bind_param('sssissssi', $username, $email, $hash, $phone, $age, $sex, $state, $address, $role_id);
if ($ins->execute()) {
    $user_id = $conn->insert_id;
    $ins->close();
    
    // set role name from roles table
    $res = $conn->query('SELECT role_name FROM roles WHERE id = ' . intval($role_id) . ' LIMIT 1');
    $role_name = 'sales_staff'; // default
    if ($res && $r = $res->fetch_assoc()) {
        $role_name = $r['role_name'];
    }
    $conn->close();
    
    // optionally auto-login user and set session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role_name;
    
    header('Location: index.html');
    exit;
} else {
    $ins->close();
    $conn->close();
    header('Location: login.html?signup=error&msg=insert');
    exit;
}
