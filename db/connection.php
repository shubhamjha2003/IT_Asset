<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'it_asset_mgmt';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
