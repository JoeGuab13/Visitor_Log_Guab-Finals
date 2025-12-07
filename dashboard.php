<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

// Filters: date / search / purpose
$filter_date = $_GET['date'] ?? date('Y-m-d');
$search = trim($_GET['search'] ?? '');
$purpose = $_GET['purpose'] ?? 'ALL';

// build query
$sql = "SELECT id, full_name, address, contact, school_office, purpose, visit_date, visit_time FROM visitors WHERE visit_date = ?";
$params = [$filter_date];
$types = 's';

if ($purpose !== 'ALL') {
    $sql .= " AND purpose = ?";
    $types .= 's';
    $params[] = $purpose;
}
if ($search !== '') {
    $sql .= " AND (full_name LIKE CONCAT('%',?,'%') OR contact LIKE CONCAT('%',?,'%') OR school_office LIKE CONCAT('%',?,'%'))";
    $types .= 'sss';
    $params[] = $search; $params[] = $search; $params[] = $search;
}
$sql .= " ORDER BY visit_time ASC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// stats
$today = date('Y-m-d');
$r_total = $mysqli->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = ?");
$r_total->bind_param('s', $today); $r_total->execute(); $r_total->bind_result($count_today); $r_total->fetch(); $r_total->close();

$r_exams = $mysqli->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = ? AND purpose = 'EXAM'");
$r_exams->bind_param('s', $today); $r_exams->execute(); $r_exams->bind_result($count_exams); $r_exams->fetch(); $r_exams->close();

$r_others = $mysqli->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = ? AND purpose = 'OTHERS'");
$r_others->bind_param('s', $today); $r_others->execute(); $r_others->bind_result($count_others); $r_others->fetch(); $r_others->close();

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - Visitor Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Visitor Log</a>
    <div class="d-flex">
      <span class="navbar-text me-3">Welcome, <?php echo e($_SESSION['fullname']); ?></span>
      <a class="btn btn-outline-light" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row mb-3">
    <div class="col-md-8">
      <h4>Visitors on <?php echo date('d M Y', strtotime($filter_date)); ?></h4>
    </div>
    <div class="col-md-4 text-end">
      <a class="btn btn-success" href="add_visitor.php">New Visitor</a>
    </div>

  <div class="row mb-3">
    <div class="col-md-9">
      <form class="row g-2" method="get">
        <div class="col-auto">
          <input type="date" name="date" class="form-control" value="<?php echo e($filter_date); ?>">
        </div>
        <div class="col-auto">
          <select name="purpose" class="form-select">
            <option value="ALL" <?php if($purpose==='ALL') echo 'selected'; ?>>All Purposes</option>
            <option value="INQUIRY" <?php if($purpose==='INQUIRY') echo 'selected'; ?>>Inquiry</option>
            <option value="EXAM" <?php if($purpose==='EXAM') echo 'selected'; ?>>Exam</option>
            <option value="VISIT" <?php if($purpose==='VISIT') echo 'selected'; ?>>Visit</option>
            <option value="OTHERS" <?php if($purpose==='OTHERS') echo 'selected'; ?>>Others</option>
          </select>
        </div>
        <div class="col-auto">
          <input type="text" name="search" class="form-control" placeholder="Search name/contact/school" value="<?php echo e($search); ?>">
        </div>
        <div class="col-auto">
          <button class="btn btn-primary">Filter</button>
        </div>
      </form>
    </div>

    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <div>Total Today</div>
          <h3><?php echo e($count_today ?? 0); ?></h3>
          <small>Exams: <?php echo e($count_exams ?? 0); ?> | Others: <?php echo e($count_others ?? 0); ?></small>
        </div>
      </div>
    </div>
  </div>

  <!-- visitors table -->
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Time</th>
          <th>Name</th>
          <th>Contact</th>
          <th>School / Office</th>
          <th>Purpose</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo e(substr($row['visit_time'],0,5)); ?></td>
            <td><?php echo e($row['full_name']); ?></td>
            <td><?php echo e($row['contact']); ?></td>
            <td><?php echo e($row['school_office']); ?></td>
            <td><?php echo e(ucfirst(strtolower($row['purpose']))); ?></td>
            <td>
              <a class="btn btn-sm btn-danger" href="delete_visitor.php?id=<?php echo e($row['id']); ?>&date=<?php echo e($filter_date); ?>" onclick="return confirm('Delete this record?');">Delete</a>
            </td>
          </tr>
        <?php endwhile; $stmt->close(); ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
