<?php
session_start();
include ("connection/connect.php");
date_default_timezone_set("Asia/Colombo");

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: account/login.php");
    exit;
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    //write query to promote ALL THE students in academic year to the next year in stu_bed table

    //Get all the academic_year by the id from academic_year table
    $query = "SELECT academic_year FROM academic_year WHERE id = $id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $academic_year = $row['academic_year'];
    //Get the next academic year by splitting the current academic year and incrementing the years
    $years = explode('/', $academic_year);
    $next_academic_year = ($years[0] + 1) . '/' . ($years[1] + 1);

    //check whether the next academic year already exists in the database
    $query = "SELECT id FROM academic_year WHERE academic_year = '$next_academic_year'";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        
    }else{
        //error message
        echo "<script>alert('Next academic year does not exist! Please add the next academic year before promoting.');</script>";
        echo "<script>window.location='addacayr.php';</script>";
        exit;
    }


    //promote 
    $query = "UPDATE academic_year SET is_promoted = 1, promoted_on = NOW() WHERE id = $id";
    $conn->query($query);

    // for all the entries in stureg_bed table update with the next academic year, like if this year is 2023/2024 then the next should be 2024/2025
    //  stureg_bed (stureg_id, bed_id, academic_year) academic_year is a int id that reference to academic_year table


    //Set all the beds that allocated to the students in the current academic year to not allocated, so that the students can be allocated to the beds in the next academic year
    $query = "UPDATE hostel_bed SET availability = 1 WHERE bed_id IN (SELECT bed_id FROM studreg_bed WHERE academic_year_id = $id)";
    $conn->query($query);



    // //first get the next academic year id from academic_year table
    // $query = "SELECT id FROM academic_year WHERE academic_year = '$next_academic_year'";
    // $result = $conn->query($query);
    // $row = $result->fetch_assoc();
    // $next_academic_year_id = $row['id'];
    // //update the stureg_bed table with the next academic year id
    // $query = "UPDATE studreg_bed SET academic_year_id = $next_academic_year_id WHERE academic_year_id = $id";
    // $conn->query($query);


    





   
   
   
   
   
   
   
   
   
   
   
   
   
    // $conn->query(query);
    
    // // Set the selected year as current
    // $stmt = $conn->prepare("UPDATE academic_year SET is_current = 1 WHERE id = ?");
    // $stmt->bind_param("i", $id);
    
    // if($stmt->execute()) {
    //     echo "<script>alert('Academic year promoted successfully!');</script>";
    // } else {
    //     echo "<script>alert('Error promoting academic year!');</script>";
    // }
    
    // $stmt->close();
    header("location: addacayr.php");
    exit;
}
?>