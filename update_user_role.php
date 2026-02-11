<?php
session_start();
// Only root users can update roles
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'root') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Forbidden - admin access only.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_users.php');
    exit;
}

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;

if ($user_id <= 0 || $role_id <= 0) {
    header('Location: admin_users.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'tealogy_login');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$stmt = $conn->prepare('UPDATE users SET role_id = ? WHERE id = ?');
$stmt->bind_param('ii', $role_id, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: admin_users.php');
exit;
