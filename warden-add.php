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
        <h2 class="text-center page-title"><i class="fa fa-user-plus"></i> Add New Warden</h2>

    <div class="container">
           
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="edituser" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <select class="form-control" id="title" name="title" required>
                            <option value="">Select a title</option>
                            <option value="Mr">Mr</option>
                            <option value="Mrs">Mrs</option>
                            <option value="Ms">Ms</option>
                            <option value="Dr">Dr</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Fullname:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter fullname" required>
                        <div class="invalid-feedback">
                            Please enter fullname.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                        <div class="invalid-feedback">
                            Please enter a valid email.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" class="form-control" id="contact" name="contact" placeholder="Enter contact number" required>
                        <div class="invalid-feedback">
                            Please enter a contact number.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="grade">Grade:</label>
                        <input type="text" class="form-control" id="grade" name="grade" placeholder="Enter grade" required>
                        <div class="invalid-feedback">
                            Please enter a grade.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="profile_image">Profile Image:</label>
                        <input type="file" class="form-control-file" id="profile_image" name="profile_image" accept="image/*" required>
                        <div class="invalid-feedback">
                            Please upload a profile image.
                        </div>
                    </div>
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
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success" name="add_user">
                        <i class="fa fa-save"></i>    
                        Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</html>

<?php
if(isset($_POST['add_user'])) {
    include ("connection/connect.php");

    $title = $conn->real_escape_string($_POST['title']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $grade = $conn->real_escape_string($_POST['grade']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // Validate input
    if (empty($title) || empty($name) || empty($email) || empty($contact) || empty($grade) || empty($username) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('Please fill in all fields.');</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    //check username exists
    $check_query = "SELECT id FROM users WHERE username='$username'";
    $check_result = $conn->query($check_query);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    //Upload profile image if provided
    $profile_image_path = null;

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['profile_image']['tmp_name'];
        $file_name = $_FILES['profile_image']['name'];
        $file_size = $_FILES['profile_image']['size'];
        $file_type = $_FILES['profile_image']['type'];
        $file_name_cmps = explode(".", $file_name);
        $file_extension = strtolower(end($file_name_cmps));

        // Sanitize file name and create unique name
        $new_file_name = md5(time() . $file_name) . '.' . $file_extension;

        // Check if the file is an image
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($file_extension, $allowed_extensions)) {
            // Move the file to the uploads directory
            $upload_file_dir = './images/';
            if (!is_dir($upload_file_dir)) {
                mkdir($upload_file_dir, 0755, true);
            }
            $dest_path = $upload_file_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $profile_image_path = $dest_path;
            } else {
                echo "<script>alert('There was an error uploading the profile image.');</script>";
                exit;
            }
        } else {
            echo "<script>alert('Only image files (jpg, jpeg, png, gif) are allowed for profile image.');</script>";
            exit;
        }
    }

    //first create user account
    $insert_user_query = "INSERT INTO users (username, password, cat_id) VALUES ('$username', '$password_hash', '2')";
    if ($conn->query($insert_user_query) === TRUE) {
        $user_id = $conn->insert_id; // Get the ID of the newly created user

        // Then create warden profile linked to the user account
        $insert_warden_query = "INSERT INTO user_warden (user_id, title, name, email, contact, grade,image) VALUES ('$user_id', '$title', '$name', '$email', '$contact', '$grade','$profile_image_path')";
        
        if ($conn->query($insert_warden_query) === TRUE) {
            echo "<script>alert('New warden added successfully!'); window.location.href='warden.php';</script>";
        } else {
            echo "<script>alert('Error creating warden profile: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error creating user account: " . $conn->error . "');</script>";
    }

}
?>