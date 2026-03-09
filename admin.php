<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role']!=="admin") {
    die("Access denied");
}
echo "<h1>Admin Dashboard</h1>";
