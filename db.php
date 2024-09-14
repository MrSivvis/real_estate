<?php
$conn = new mysqli('localhost', 'root', '', 'real_estate_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>