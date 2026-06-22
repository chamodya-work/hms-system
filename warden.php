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
        <h2 class="text-center page-title"><i class="fa fa-users"></i> Warden Management</h2>
    
        <div class="container">
            
            
            <!--Form starts here-->
            <form id="usermanagement" action="" method="post" class="needs-validation" novalidate>
                
                <!-- Input Row -->
                <div class="row justify-content-center mb-4">
                    
                    <!-- Search -->	
                    <div class="col-md-4 col-lg-3">
                        <label for="search">Search by Name</label>
                        <input type="text" 
                            class="form-control" 
                            id="search"
                            name="search"
                            placeholder="Enter username or email"
                            value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                    </div>
                    
                    <!-- Add User Button -->
                    <div class="col-md-4 col-lg-3 align-self-end">
                        <a href="warden-add.php" class="btn btn-success text-white w-100"><i class="fa fa-plus"></i> Add New Warden</a>
                    </div>
                </div>

                <!-- User Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Fullname</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch users from the database based on search and filter criteria
                            include ("connection/connect.php");
                            $search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';
                            $query = "SELECT * FROM user_warden WHERE name LIKE '%$search%' OR email LIKE '%$search%'";

                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
                                    echo "<td>
                                            <a href='warden-edit.php?id=" . $row['warden_id'] . "' class='btn text-white btn-warning btn-sm'><i class='fa fa-edit'></i> Edit</a>
                                            <a href='warden-delete.php?id=" . $row['warden_id'] . "' class='btn text-white btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this warden? This action will delete associated user account as well.');\"><i class='fa fa-trash'></i> Delete</a>
                                            <a href='allocate-blocks.php?id=" . $row['warden_id'] . "' class='btn text-white btn-success btn-sm'><i class='fa fa-plus '></i> De/Allocate Blocks</a>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            
        </div>
        <!-- footer-->
        <?php include 'footer.php'; ?>
    </body>
</html>

