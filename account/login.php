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
        // Check user category
        if ($_POST["usercat"] == "stu") {
            include '../getData.php';
            $response = stuData($username, $password);

            if (isset($response['state']) && $response['state'] == true) {
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["cat"] = "1";
                $_SESSION['last_time'] = time();
                header("location: ../index.php");
            } else {
                $password_err = "The username or password you entered was not valid.";
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

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["cat"] = $cat;
                                $_SESSION['last_time'] = time();



                                if ($cat == 2) {
                                    //get the warden id from the user_warden table
                                    $query = "SELECT warden_id FROM user_warden WHERE user_id = $id";
                                    $result = $conn->query($query);
                                    if ($result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        $_SESSION["warden_id"] = $row['warden_id'];
                                    }
                                }

                                if ($cat === 4) {
                                    $query = "SELECT category FROM user_supervisor WHERE user_id = $id";
                                    $result = $conn->query($query);
                                    if ($result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        $_SESSION['supervisor_category'] = $row['category'];
                                    } else {
                                        $_SESSION['supervisor_category'] = null;
                                    }
                                }


                                $sql_id = "SELECT hosreg_id FROM hostel_reg WHERE NOW()>regc";
                                $id_run = mysqli_query($conn, $sql_id);
                                while ($id_raw = mysqli_fetch_assoc($id_run)) {
                                    $hosreg_id = $id_raw['hosreg_id'];
                                    $sql_del = "UPDATE hostel_room SET reserved = '0', hosreg_id = '0' WHERE hosreg_id = $hosreg_id AND reserved = '1'; ";
                                    $del_run = mysqli_query($conn, $sql_del);
                                }
                                // Redirect user to welcome page
                                header("location: ../index.php");
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

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../images/icons/logo.png" />

    <title>Hostel Management System - Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #b57a2c 0%, #8b5a1f 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            padding: 50px 40px 40px;
            text-align: center;
        }

        .logo-section {
            margin-bottom: 30px;
        }

        .logo-section img {
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .login-card h2 {
            color: #b57a2c;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 28px;
        }

        .login-card p {
            color: #999;
            font-size: 13px;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        .user-type-section {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
        }

        .user-type-option {
            flex: 1;
            position: relative;
        }

        .user-type-option input[type="radio"] {
            display: none;
        }

        .user-type-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #666;
            background-color: #f9f9f9;
            font-size: 14px;
        }

        .user-type-option input[type="radio"]:checked+label {
            background-color: #b57a2c;
            color: white;
            border-color: #b57a2c;
            box-shadow: 0 4px 12px rgba(181, 122, 44, 0.25);
        }

        .user-type-option label:hover {
            border-color: #b57a2c;
            background-color: #faf8f5;
        }

        .user-type-option input[type="radio"]:checked+label:hover {
            background-color: #a06b23;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            outline: none;
            border-color: #b57a2c;
            box-shadow: 0 0 0 3px rgba(181, 122, 44, 0.1);
            background-color: #fffbf7;
        }

        .help-block {
            display: block;
            margin-top: 5px;
            font-size: 13px;
            color: #dc3545;
        }

        .form-group.has-error input {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background-color: #b57a2c;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background-color: #a06b23;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 90, 31, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">

            <!-- Logo -->
            <div class="logo-section">
                <img src="../images/logoM.png" alt="Hostel Management Logo">
            </div>
            <hr>
            <h2>HOSTEL MANAGEMENT SYSTEM</h2>
            <p>Please sign in to your account</p>
            <hr>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <!-- User Type Selection -->
                <div class="user-type-section">
                    <div class="user-type-option">
                        <input type="radio" id="student" class="form-check-input" name="usercat" value="stu" required>
                        <label for="student">👤 Student</label>
                    </div>
                    <div class="user-type-option">
                        <input type="radio" id="staff" class="form-check-input" name="usercat" value="staff" required>
                        <label for="staff">👨‍💼 Staff</label>
                    </div>
                </div>

                <!-- Username Field -->
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control"
                        value="<?php echo $username; ?>" required>
                    <?php if (!empty($username_err)): ?>
                        <span class="help-block"><?php echo $username_err; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Password Field -->
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <?php if (!empty($password_err)): ?>
                        <span class="help-block"><?php echo $password_err; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login">Sign In</button>

            </form>
        </div>
    </div>

</body>

</html>
