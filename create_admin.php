<?php
require 'config.php';
$username = 'admin';
$password_plain = 'admin123';
$fullname = 'Administrator';

$hash = password_hash($password_plain, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) { echo "User exists."; exit; }
$stmt->close();

$stmt = $mysqli->prepare("INSERT INTO users (username, password, fullname) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $hash, $fullname);
$stmt->execute();
echo "Admin created.";
?>