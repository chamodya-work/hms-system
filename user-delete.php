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

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    //delete the user from the database
    $query = "DELETE FROM users WHERE id = $id";
    if($conn->query($query) === TRUE) {
        echo "<script>alert('User deleted successfully!');</script>";
        echo "<script>window.location='user.php';</script>";
        exit;
    }else{
        echo "<script>alert('Error deleting user!');</script>";
        echo "<script>window.location='user.php';</script>";
        exit;
    }
}else{
    echo "<script>alert('Invalid user ID!');</script>"; 
    echo "<script>window.location='user.php';</script>";
    exit;
}
?>