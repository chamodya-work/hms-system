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
        <h2 class="text-center page-title"><i class="fa fa-user-edit"></i> Edit User Information</h2>

    <div class="container">
        
<?php
// load the user information by getting it from the table with id and then update the information in the database with the new information

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch user information from the database
    $query = "SELECT username, cat_id FROM users WHERE id = $id";
    $result = $conn->query($query);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $username = $row['username'];
        $category = $row['cat_id'];
    } else {
        echo "<script>alert('User not found!');</script>";
        echo "<script>window.location='user.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid user ID!');</script>";
    echo "<script>window.location='user.php';</script>";
    exit;
}




?>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="edituser" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        <div class="invalid-feedback">
                            Please enter a username.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        <div class="invalid-feedback">
                            Please enter a password.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                        <div class="invalid-feedback">
                            Please confirm the password.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">Select a category</option>
                            <option value="1" <?php if($category == '1') echo 'selected'; ?>>Student</option>
                            <option value="3" <?php if($category == '3') echo 'selected'; ?>>Secretary</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a category.
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success" name="update_user">
                        <i class="fa fa-save"></i>    
                        Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</html>

<?php
if(isset($_POST['update_user'])) {

    $id = intval($_POST['id']);
    $username = $conn->real_escape_string($_POST['username']);
    $category = $conn->real_escape_string($_POST['category']);

    if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {

        if ($_POST['password'] === $_POST['confirm_password']) {

            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $update_query = "UPDATE users 
                             SET username='$username', 
                                 cat_id='$category', 
                                 password='$password_hash' 
                             WHERE id=$id";

        } else {
            echo "<script>alert('Passwords do not match!');</script>";
            exit;
        }

    } else {

        $update_query = "UPDATE users 
                         SET username='$username', 
                             cat_id='$category' 
                         WHERE id=$id";
    }

    if ($conn->query($update_query)) {
        echo "<script>alert('User updated successfully');</script>";
        echo "<script>window.location='user.php';</script>";
    } else {
        echo $conn->error;
    }
}
?>