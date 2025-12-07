<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $school_office = trim($_POST['school_office'] ?? '');
    $purpose = $_POST['purpose'] ?? 'INQUIRY';
    $visit_date = $_POST['visit_date'] ?? date('Y-m-d');
    $visit_time = $_POST['visit_time'] ?? date('H:i');

    if ($full_name === '') $errors[] = 'Visitor name is required.';
    if (!in_array($purpose, ['INQUIRY','EXAM','VISIT','OTHERS'])) $purpose = 'INQUIRY';

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO visitors (full_name, address, contact, school_office, purpose, visit_date, visit_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $full_name, $address, $contact, $school_office, $purpose, $visit_date, $visit_time);
        if ($stmt->execute()) {
            header('Location: dashboard.php?date='.$visit_date);
            exit;
        } else {
            $errors[] = 'DB Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>New Visitor - Visitor Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between mb-3">
    <h4>Log New Visitor</h4>
    <div>
      <a class="btn btn-secondary" href="dashboard.php">Back</a>
    </div>
  </div>

  <?php if($errors): ?>
    <div class="alert alert-danger">
      <ul><?php foreach($errors as $er) echo "<li>".e($er)."</li>"; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="mb-3">
      <label class="form-label">Full Name *</label>
      <input name="full_name" class="form-control" value="<?php echo e($_POST['full_name'] ?? ''); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input name="address" class="form-control" value="<?php echo e($_POST['address'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Contact #</label>
      <input name="contact" class="form-control" value="<?php echo e($_POST['contact'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">School / Office</label>
      <input name="school_office" class="form-control" value="<?php echo e($_POST['school_office'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Purpose</label>
      <select name="purpose" class="form-select">
        <option value="INQUIRY">Inquiry</option>
        <option value="EXAM">Exam</option>
        <option value="VISIT">Visit</option>
        <option value="OTHERS">Others</option>
      </select>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="visit_date" class="form-control" value="<?php echo e($_POST['visit_date'] ?? date('Y-m-d')); ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Time</label>
        <input type="time" name="visit_time" class="form-control" value="<?php echo e($_POST['visit_time'] ?? date('H:i')); ?>">
      </div>
    </div>
    <div class="d-grid">
      <button class="btn btn-primary">Save Visitor</button>
    </div>
  </form>
</div>
</body>
</html>
