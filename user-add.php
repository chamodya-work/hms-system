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
        <h2 class="text-center page-title"><i class="fa fa-user-plus"></i> Add New User</h2>

    <div class="container">
           
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="edituser" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                        <div class="invalid-feedback">
                            Please enter a username.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                        <div class="invalid-feedback">
                            Please enter a password.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                        <div class="invalid-feedback">
                            Please confirm the password.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">Select a category</option>
                            <option value="1">Student</option>
                            <option value="3">Secretary</option>
                            <option value="4">Supervisor</option> 
                        </select>
                        <div class="invalid-feedback">
                            Please select a category.
                        </div>
                    </div>
                    <div class="form-group" id="supervisor_category_group" style="display:none;">
                        <label for="supervisor_category">Supervisor Category:</label>
                        <select class="form-control" id="supervisor_category" name="supervisor_category" required>
                            <option value="">Select a category</option>
                            <option value="1">Civil</option>
                            <option value="2">Electrical</option>
                            <option value="3">Health</option>
                            <option value="4">Landscape</option>
                            <option value="5">Cleaning</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a role.
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success" name="add_user">
                        <i class="fa fa-save"></i>    
                        Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const user_category = document.getElementById('category');
        const supervisor_category_group = document.getElementById('supervisor_category_group');

        user_category.addEventListener('change', function() {
            if (this.value === '4') { // Supervisor
                supervisor_category_group.style.display = 'block';
                document.getElementById('supervisor_category').setAttribute('required', 'required');
            } else {
                supervisor_category_group.style.display = 'none';
                document.getElementById('supervisor_category').removeAttribute('required');
            }
        });
    </script>
</html>

<?php
if(isset($_POST['add_user'])) {
    include ("connection/connect.php");

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $category = $conn->real_escape_string($_POST['category']);

    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($category)) {
        echo "<script>alert('Please fill in all fields.');</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit;
    }

    // Check if username already exists
    $check_query = "SELECT id FROM users WHERE username = '$username'";
    $check_result = $conn->query($check_query);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        exit;
    }

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    // Insert new user into the database
    $insert_query = "INSERT INTO users (username, password, cat_id) VALUES ('$username', '$hashed_password', '$category')";
    
    if ($conn->query($insert_query) === TRUE) {

        if($category==4){
            $supervisor_category = $conn->real_escape_string($_POST['supervisor_category']);
            $user_id = $conn->insert_id; // Get the ID of the newly inserted user
            $supervisor_query = "INSERT INTO user_supervisor (user_id, category) VALUES ('$user_id', '$supervisor_category')";
            $conn->query($supervisor_query);
        }


        echo "<script>alert('User added successfully');</script>";
        echo "<script>window.location='user.php';</script>";
    } else {
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }

    if ($category === '2') {
        $user_id = $conn->insert_id;
        $warden_id = $_POST['warden_id']; // Assuming you have a field for warden ID in the form
        $subwarden_query = "INSERT INTO user_warden (user_id) VALUES ('$user_id')";
        $conn->query($subwarden_query);
    }

}
?>

