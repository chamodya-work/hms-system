//Delete a record from the user table
<?php
// Initialize the session
session_start();
		include ("connection/connect.php");
		date_default_timezone_set("Asia/Colombo");
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: account/login.php");
    exit;
}
if($_SESSION["cat"] != '3') {
    header("location: index.php");
    exit;
}

if(isset($_GET['warden_id']) && isset($_GET['OP']) && isset($_GET['hos_id'])) {
    $id = $_GET['warden_id'];
    $hos_id = $_GET['hos_id'];
    $operation = $_GET['OP'];

    if($operation == "allocate") {
        $query = "INSERT INTO warden_hostel (warden_id, hos_id) VALUES ($id, '$hos_id')";
    } else if($operation == "deallocate") {
        $query = "DELETE FROM warden_hostel WHERE warden_id = $id AND hos_id = '$hos_id'";
    } else {
        echo "<script>alert('Invalid operation!');</script>"; 
        echo "<script>window.location='warden.php';</script>";
        exit;
    }

    if($conn->query($query) === TRUE) {
        echo "<script>alert('Operation successful!');</script>";
        echo "<script>window.location='allocate-blocks.php?id=$id';</script>";
        exit;
    }else{
        echo "<script>alert('Error performing operation!');</script>";
        echo "<script>window.location='allocate-blocks.php?id=$id';</script>";
        exit;
    }
}else{
    echo "<script>alert('Invalid request!');</script>";
    echo "<script>window.location='warden.php';</script>";
    exit;
}
?>