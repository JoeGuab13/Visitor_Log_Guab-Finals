<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

$id = intval($_GET['id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');
if ($id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM visitors WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
header('Location: dashboard.php?date='.$date);
exit;
?>
