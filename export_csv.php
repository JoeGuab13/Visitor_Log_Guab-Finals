<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

$date = $_GET['date'] ?? date('Y-m-d');

$stmt = $mysqli->prepare("SELECT full_name, address, contact, school_office, purpose, visit_time FROM visitors WHERE visit_date = ? ORDER BY visit_time ASC");
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();

$filename = "visitors_" . $date . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
$output = fopen('php://output', 'w');
fputcsv($output, ['Name','Address','Contact','School/Office','Purpose','Time']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['full_name'],
        $row['address'],
        $row['contact'],
        $row['school_office'],
        $row['purpose'],
        $row['visit_time']
    ]);
}

fclose($output);
exit;
?>
