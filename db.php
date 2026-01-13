<?php
$conn = mysqli_connect("localhost", "root", "", "registration_db");


if (!$conn) {
die("Database connection failed: " . mysqli_connect_error());
}
?>