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
        <h2 class="text-center page-title"><i class="fa fa-users"></i> User Management</h2>
    
        <div class="container">
            
            
            <!--Form starts here-->
            <form id="usermanagement" action="" method="post" class="needs-validation" novalidate>
                
                <!-- Input Row -->
                <div class="row justify-content-center mb-4">
                    
                    <!-- Search -->	
                    <div class="col-md-6 col-lg-6">
                        <label for="search">Search by Username</label>
                        <input type="text" 
                            class="form-control" 
                            id="search"
                            name="search"
                            placeholder="Enter username or email"
                            value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                    </div>
                    
                    <!-- Add User Button -->
                    <div class="col-md-2 col-lg-2 col align-self-end">
                        <a href="user-add.php" class="btn btn-success text-white w-100"><i class="fa fa-plus"></i> Add Other User</a>
                    </div>
                    <div class="col-md-3 col-lg-3 col align-self-end">
                        <a href="warden-add.php" class="btn btn-success text-white w-100 mt-2"><i class="fa fa-plus"></i> Add New Warden</a>
                    </div>
                </div>

                <!-- User Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Category</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch users from the database based on search and filter criteria
                            include ("connection/connect.php");
                            $search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';
                            $category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';

                            $query = "SELECT id,username, cat_id, created_at FROM users WHERE (username LIKE '%$search%' OR cat_id='$category') ORDER BY created_at DESC";
                           
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td>" . ($row['cat_id'] == '1' ? 'Student' : ($row['cat_id'] == '2' ? 'Subwarden' : 'Secretary')) . "</td>";
                                    echo "<td>" . date('d M Y', strtotime($row['created_at'])) . "</td>";
                                    echo "<td><a href='user-edit.php?id=" . urlencode($row['id']) . "' class='btn btn-sm text-white btn-warning'><i class='fa fa-edit'></i> Edit</a> <a href='user-delete.php?id=" . urlencode($row['id']) . "' class='btn btn-sm text-white btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\");'><i class='fa fa-trash'></i> Delete</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No users found.</td></tr>";
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

