<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

// Include config file
require_once "../connection/connect.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";



// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {



    // Check if username is empty

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {

        //check user category
        if ($_POST["usercat"] == "stu") {

            include '../getData.php';
            $response = stuData($username, $password);
            if ($response['state'] == true) {



                // Store data in session variables
                $_SESSION["loggedin"] = true;
                //$_SESSION["id"] = "1";
                $_SESSION["username"] = $username;
                $_SESSION["cat"] = "1";
                $_SESSION['last_time'] = time();
                header("location: ../index.php");
            }
        } else {

            $sql = "SELECT id, username, password, cat_id FROM users WHERE username = ?";

            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("s", $param_username);

                // Set parameters
                $param_username = $username;

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Store result
                    $stmt->store_result();

                    // Check if username exists, if yes then verify password
                    if ($stmt->num_rows == 1) {
                        // Bind result variables
                        $stmt->bind_result($id, $username, $hashed_password, $cat);
                        if ($stmt->fetch()) {
                            if (password_verify($password, $hashed_password)) {
                                // Password is correct, so start a new session
                                session_start();

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["cat"] = $cat;
                                $_SESSION['last_time'] = time();
                                if ($cat === 4) {
                                    // Prepare statement to prevent SQL injection
                                    $stmt = $conn->prepare("SELECT category FROM user_supervisor WHERE user_id = ?");
                                    $_SESSION['supervisor_category'] = "Hello Boy";
                                }
                                echo "<script>console.log('".$_SESSION['supervisor_category']."');</script>";
                                $sql_del = "UPDATE hostel_bed SET reserved = '0', hosreg_id = '0' WHERE hosreg_id = (SELECT hosreg_id FROM hostel_reg WHERE NOW()>regc); ";
                                $del_run = mysqli_query($conn, $sql_del);

                                // Redirect user to welcome page
                                // header("location: ../index.php");
                            } else {
                                // Display an error message if password is not valid
                                $password_err = "The password you entered was not valid.";
                            }
                        }
                    } else {
                        // Display an error message if username doesn't exist
                        $username_err = "No account found with that username.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }

            // Close statement
            $stmt->close();
        }

    }




    /*
    // Validate credentials
    //if(empty($username_err) && empty($password_err)){
        // Prepare a select statement

    //}
    */
    // Close connection
    $conn->close();
}

?>




<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <link rel="icon" type="image/png" href="../images/icons/logo.png" />

    <title>Hostel Management System</title>
</head>

<body
    style="margin:0;padding:10px 3%; background-image: url(../images/back1.jpg);background-position: top;background-repeat: repeat;background-size: cover;">


    <div class="fluid-container" style="padding-bottom:0px;padding-top:10px;margin-bottom:0px;height:100%;">

        <div class="row" style="padding:10px; background-color:#b57a2c">
            <div class="col-3">

                <img class="img-fluid " src="../images/logoM.png" />


            </div>
            <div class="col-9">


            </div>

        </div>
        <div class="row">
            <div class="" style="margin:auto">
                <div style="width: 350px; padding: 20px; margin: auto;">
                    <h2 style="margin-bottom:20px;">Login</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="usercat" value="stu" required>I'm a
                                Student
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="usercat" value="staff" required>I'm
                                Staff
                            </label>
                        </div>
                        <div style="margin-top:20px;">
                            <p>Please fill in your credentials to login.</p>
                        </div>
                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                            <span class="help-block text-danger"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control">
                            <span class="help-block text-danger"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="Login">

                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- footer -->
        <?php include '../footer.php'; ?>


    </div>



</body>

</html>