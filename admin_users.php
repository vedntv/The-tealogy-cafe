<?php
session_start();
// Only root users can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'root') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Forbidden - admin access only.';
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'tealogy_login');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$roles = [];
$res = $conn->query('SELECT id, role_name FROM roles');
while ($r = $res->fetch_assoc()) { $roles[$r['id']] = $r['role_name']; }

$users = $conn->query('SELECT u.id,u.username,u.email,u.role_id,r.role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.id');

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - User Roles</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="role-ui.css">
  <style>
    body{padding:24px;background:#f8f6f2}
    .container{max-width:960px}
    .role-select{min-width:180px}
  </style>
</head>
<body>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Admin — Manage User Roles</h2>
      <div><a class="btn btn-secondary" href="index.html">Back</a></div>
    </div>

    <table class="table table-striped table-bordered">
      <thead>
        <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Action</th></tr>
      </thead>
      <tbody>
<?php while ($u = $users->fetch_assoc()) { ?>
        <tr>
          <td><?php echo htmlspecialchars($u['id']); ?></td>
          <td><?php echo htmlspecialchars($u['username']); ?></td>
          <td><?php echo htmlspecialchars($u['email']); ?></td>
          <td><?php echo htmlspecialchars($u['role_name'] ?? 'sales_staff'); ?></td>
          <td>
            <form method="post" action="update_user_role.php" class="d-flex align-items-center">
              <input type="hidden" name="user_id" value="<?php echo intval($u['id']); ?>">
              <select name="role_id" class="form-select form-select-sm role-select me-2">
                <?php foreach ($roles as $rid => $rname) { $sel = ($rid == $u['role_id']) ? 'selected' : ''; echo "<option value='".intval($rid)."' $sel>".htmlspecialchars($rname)."</option>"; } ?>
              </select>
              <button class="btn btn-sm btn-primary" type="submit">Update</button>
            </form>
          </td>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
