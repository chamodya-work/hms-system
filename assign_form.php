<?php

$rep_id = $_POST["rep_id"];
$status = $_POST["status"];

include("connection/connect.php");

$update_query = "UPDATE repairs SET status='$status' WHERE rep_id='$rep_id'";

$conn->query($update_query);

// redirect back to assigned.php after updating the status
echo "<script>window.location='assigned.php';</script>";

if (isset($_POST["supervisor_review_request"])){
    $rep_id = $_POST["rep_id"];
    $supervisor_review = $_POST["supervisor_review"];
    $update_query = "UPDATE repairs SET supervisor_review='$supervisor_review' WHERE rep_id='$rep_id'";
    $conn->query($update_query);
    echo "<script>window.location='assigned.php';</script>";
}



?>