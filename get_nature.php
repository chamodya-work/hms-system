<?php
// include 'config.php'; // DB connection
include("connection/connect.php");
if (isset($_GET['cat'])) {
    $cat = $_GET['cat'];

    $sql = "SELECT n.nature_id, n.english 
            FROM nature_request n
            JOIN repair_cat r ON n.repair_cat_id = r.cat_id
            WHERE r.name = '$cat'";

    $result = mysqli_query($conn, $sql);

    echo "<option value=''>--Select Nature--</option>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['nature_id'] . "'>" . $row['english'] . "</option>";
    }
}
?>