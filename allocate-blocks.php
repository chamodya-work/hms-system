<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: account/login.php");
    exit;
}
if($_SESSION["cat"] != '3') {
    header("location: index.php");
    exit;
}


?>

<!doctype html>
<html lang="en">
    <!-- header-->
        <?php include 'header.php'; ?>
        <h2 class="text-center page-title"><i class="fa fa-user-edit"></i> Allocate/Deallocate Blocks</h2>

    <div class="container">
        
<?php
// load the user information by getting it from the table with id and then update the information in the database with the new information

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch user information from the database
    //   List all the hostels available as table better to run a query like select from hostel outer join warden_hostel 

    $query = "SELECT h.hos_id, wh.warden_id,h.gender
            FROM hostel h
            LEFT JOIN warden_hostel wh 
            ON h.hos_id = wh.hos_id AND wh.warden_id = $id";
    $result = $conn->query($query);


    $queryWarden = "SELECT name FROM user_warden WHERE warden_id = $id";
    $resultWarden = $conn->query($queryWarden);


    $totalQuery = "SELECT COUNT(*) as total FROM warden_hostel WHERE warden_id = $id";
    $totalResult = $conn->query($totalQuery);
}




?>
        
        <!-- Display Warden details as a disable form  -->
            <div class="row justify-content-left">
                <div class="col-md-6">
                    <form id="edituser" method="post" class="needs-validation" novalidate>
                        <div class="form-group">
                            <label for="name">Warden Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($resultWarden->fetch_assoc()['name']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="email">Total Allocated Blocks:</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($totalResult->fetch_assoc()['total']); ?>" disabled>
                        </div>
                    </form>
                </div>  
            </div>
        <table
            class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Hostel Name</th>
                    <th>Gender</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $isallocated = !is_null($row['warden_id']);
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['hos_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                        echo "<td>";

                        if($isallocated) {
                            echo "<a href='allocate.php?warden_id=" . $id . "&hos_id=" . $row['hos_id'] . "&OP=deallocate' class='btn text-white btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to deallocate this block?');\"><i class='fa fa-trash'></i> Deallocate</a>";
                        } else {
                            echo "<a href='allocate.php?warden_id=" . $id . "&hos_id=" . $row['hos_id'] . "&OP=allocate' class='btn text-white btn-success btn-sm'><i class='fa fa-plus'></i> Allocate</a>";
                        }
                                
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No hostels allocated to this warden.</td></tr>";
                }
                ?>
            </tbody>
    </div>
</html>
