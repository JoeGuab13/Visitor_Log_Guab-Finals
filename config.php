<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'visitor_log_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_errno) {
    die("DB Connection failed: " . $mysqli->connect_error);
}

function e($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

session_start();
?>