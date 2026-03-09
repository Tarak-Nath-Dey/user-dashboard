<?php
// Production-safe config

ini_set('display_errors', 0);
error_reporting(0);

// InfinityFree DB credentials
$DB_HOST = "sql305.infinityfree.com";
$DB_USER = "if0_40729135";
$DB_PASS = "yjvMa2DKdt9OUP6";
$DB_NAME = "if0_40729135_userdata";

// IMPORTANT: create $mycon (not $conn)
$mycon = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mycon->connect_error) {
    die("Database connection failed");
}

$mycon->set_charset("utf8mb4");
