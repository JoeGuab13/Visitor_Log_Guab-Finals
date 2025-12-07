<?php
require 'config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php'); exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $msg = 'Enter username and password.';
    } else {
        $stmt = $mysqli->prepare("SELECT id, password, fullname FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash, $fullname);
        if ($stmt->fetch() && password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['fullname'] = $fullname;
            header('Location: dashboard.php'); exit;
        } else {
            $msg = 'Invalid credentials.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - Visitor Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Visitor Log — Login</h4>
          <?php if($msg): ?>
            <div class="alert alert-warning"><?php echo e($msg); ?></div>
          <?php endif; ?>
          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary">Login</button>
              <a href="login.php" class="btn btn-outline-secondary">Reset</a>
            </div>
          </form>
          <small class="text-muted mt-2 d-block"></small>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
