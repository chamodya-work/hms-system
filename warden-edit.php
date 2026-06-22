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
        <h2 class="text-center page-title"><i class="fa fa-user-edit"></i> Edit Warden Information</h2>

    <div class="container">
        
<?php
// load the user information by getting it from the table with id and then update the information in the database with the new information

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch user information from the database
    $query = "SELECT * FROM user_warden WHERE warden_id = $id";
    $result = $conn->query($query);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $name = $row['name'];
        $email = $row['email'];
        $contact = $row['contact'];
        $grade = $row['grade'];
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
                        <label for="title">Title:</label>
                        <select class="form-control" id="title" name="title" required>
                            <option value="">Select a title</option>
                            <option value="Mr" <?php if($title == 'Mr') echo 'selected'; ?>>Mr</option>
                            <option value="Mrs" <?php if($title == 'Mrs') echo 'selected'; ?>>Mrs</option>
                            <option value="Ms" <?php if($title == 'Ms') echo 'selected'; ?>>Ms</option>
                            <option value="Dr" <?php if($title == 'Dr') echo 'selected'; ?>>Dr</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Fullname:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        <div class="invalid-feedback">
                            Please enter fullname.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <div class="invalid-feedback">
                            Please enter a valid email.
                        </div> 
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                        <div class="invalid-feedback">
                            Please enter a contact number.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="grade">Grade:</label>
                        <input type="text" class="form-control" id="grade" name="grade" value="<?php echo htmlspecialchars($grade); ?>" required>
                        <div class="invalid-feedback">
                            Please enter a grade.
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
    $title = $_POST['title'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $grade = $_POST['grade'];

    // Update user information in the database
    $query = "UPDATE user_warden SET title='$title', name='$name', email='$email', contact='$contact', grade='$grade' WHERE warden_id=$id";
    if ($conn->query($query) === TRUE) {
        echo "<script>alert('User updated successfully!');</script>";
        echo "<script>window.location='warden.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating user: " . $conn->error . "');</script>";
    }
}
?>