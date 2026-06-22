<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
   header("location: login.php");
  exit;
  }
 
// Include config file
require_once "../connection/connect.php";
 
// Define variables and initialize with empty values
$username = $new_password = $confirm_password = "";
$username_err = $new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	//get username
	if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
		//$username = $_POST["username"];
        $sql1 = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = $conn->prepare($sql1)){
			
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
				
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
					
					// Validate new password
					if(empty(trim($_POST["new_password"]))){
						$new_password_err = "Please enter the new password.";     
					} elseif(strlen(trim($_POST["new_password"])) < 6){
						$new_password_err = "Password must have atleast 6 characters.";
					} else{
						$new_password = trim($_POST["new_password"]);
					}
					
					// Validate confirm password
					if(empty(trim($_POST["confirm_password"]))){
						$confirm_password_err = "Please confirm the password.";
					} else{
						$confirm_password = trim($_POST["confirm_password"]);
						if(empty($new_password_err) && ($new_password != $confirm_password)){
							$confirm_password_err = "Password did not match.";
						}
					}
						
					// Check input errors before updating the database
					if(empty($new_password_err) && empty($confirm_password_err)){
						// Prepare an update statement
						$param_password = password_hash($new_password, PASSWORD_DEFAULT);
						$sql = "UPDATE users SET password = '".$param_password."' WHERE username = '".$param_username."' ";
						//echo $sql;
						$result = mysqli_query($conn,$sql);
						if($result){
							// Password updated successfully. Destroy the session, and redirect to login page
								$message = "Password updated successfully";
								echo "<script type='text/javascript'>alert('$message');</script>";
								echo "<meta http-equiv='refresh' content='0'>";
						}
						else
							echo "Oops! Something went wrong. Please try again later.";
						
						
						// Close statement
						$stmt->close();
					}
                   
                } else{
					
                    $username_err = "This username invalid.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        
    }
    
    
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
	
	<link rel="icon" type="image/png" href="../images/icons/logo.png"/>
	
    <title>Staff Directory</title>
  </head>
  <body style="margin:0 10%;padding:10px;">
	<div class="jumbotron jumbotron-fluid" style="padding-bottom:0px;padding-top:10px;margin-bottom:0px;">
		<div class="container">
		<div class="row">
			<div class="col-sm-4">
				<a href="index.php"><img class="img-fluid" src="../images/logoM.png" /></a>
			</div>
			<div class="col-sm-8">
				<h5 class="text-right"><b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></h5>
			</div>
		</div>	
		</div>
		<!-- menu bar-->
		
	</div>
	
	
	
	<div class="container" style="padding:30px; background-image: url(../images/back1.jpg);background-position: center;background-repeat: no-repeat;background-size: cover;">
		
		<div style="width: 350px; padding: 20px; margin: auto;">
         <h2>Reset Password</h2>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    		
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-danger" href="../index.php">Cancel</a>
            </div>
        </form>
    </div>    
	
	
	
	
	</div>
	<!-- footer -->
	<?php include '../footer.php'; ?>
	
 </body>
</html>