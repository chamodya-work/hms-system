<?php
$servername = "127.0.0.1";
// for server
// $username = "hostel_user";
// $password = "hostel_user";
// $db = "hostel_db";

// for local
$username = "root";
$password = "";
$db = "hms";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";


