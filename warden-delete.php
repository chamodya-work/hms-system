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
    //get user id by querying the table user_warden
    $query = "SELECT * FROM user_warden WHERE warden_id = $id";
    $result = $conn->query($query);
    if ($result->num_rows != 1) {
        echo "<script>alert('Invalid warden ID!');</script>";
        echo "<script>window.location='warden.php';</script>";
        exit;
    }
    $user_id = $result->fetch_assoc()['user_id'];
    //delete the user from the database
    $query = "DELETE FROM user_warden WHERE warden_id = $id";


    if($_SESSION["id"] == $user_id) {
        echo "<script>alert('You cannot delete your own account!');</script>";
        echo "<script>window.location='warden.php';</script>";
        exit;
    }
    
    if($conn->query($query) === TRUE) {
        //delete the user from the users table as well
        $query = "DELETE FROM users WHERE id = $user_id";
        $conn->query($query);

        echo "<script>alert('Warden deleted successfully!');</script>";
        echo "<script>window.location='warden.php';</script>";
        exit;
    }else{
        echo "<script>alert('Error deleting warden!');</script>";
        echo "<script>window.location='warden.php';</script>";
        exit;
    }
}else{
    echo "<script>alert('Invalid warden ID!');</script>"; 
    echo "<script>window.location='warden.php';</script>";
    exit;
}
?>