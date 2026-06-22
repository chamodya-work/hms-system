<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: account/login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
  <!-- header-->
	<?php include 'header.php'; ?>
	<div class="container" >
		<h2 class="text-center"><br>Edit Hostel Details</h2><br><br>
		
		<!--Form starts here-->
		<form id="edithostel" action=""  method="post" class="main-form needs-validation"  novalidate>
			<?php
			if(isset($_GET['hos_id'])){
				
				//get hostel details
				$hos_id = $_GET['hos_id'];
				

				// Prepared statement for fetching hostel details
	            $select = "SELECT * FROM hostel WHERE hos_id = ?";
	            if ($stmt = mysqli_prepare($conn, $select)) {
	                mysqli_stmt_bind_param($stmt, "i", $hos_id);
	                mysqli_stmt_execute($stmt);
	                mysqli_stmt_bind_result($stmt, $hos_id, $hos_rooms, $hos_ppr, $gender);
	                mysqli_stmt_fetch($stmt);
	                mysqli_stmt_close($stmt);
	            }
			
			?>
			
			div class="form-row">
            <div class="form-group col-md-2"></div>
            
	            <!-- Hostel ID (Readonly) -->
	            <div class="form-group col-md-2">
	                <label for="hos_id">Hostel ID:</label>
	                <input type="text" class="form-control" value="<?php echo htmlspecialchars($hos_id); ?>" name="hos_id" readonly>
	            </div>

	            <!-- Number of Rooms -->
	            <div class="form-group col-md-2">
	                <label for="hos_rooms">No. of Rooms:</label>
	                <input type="number" class="form-control" value="<?php echo htmlspecialchars($hos_rooms); ?>" name="hos_rooms" required>
	            </div>

	            <!-- Students per Room -->
	            <div class="form-group col-md-2">
	                <label for="hos_ppr">Students per Room:</label>
	                <input type="number" class="form-control" value="<?php echo htmlspecialchars($hos_ppr); ?>" name="hos_ppr" required>
	            </div>

	            <!-- Gender -->
	            <div class="form-group col-md-2">
	                <label for="gender">Gender:</label>
	                <select class="form-control" id="gender" name="gender" required>
	                    <option value="M" <?php echo ($gender == "M") ? 'selected' : ''; ?>>Male</option>
	                    <option value="F" <?php echo ($gender == "F") ? 'selected' : ''; ?>>Female</option>
	                </select>
	            </div>
	            
	            <div class="form-group col-md-2"></div>
	        </div>

	        <!-- Submit Button -->
	        <div class="form-group" style="text-align:center;">
	            <button type="submit" class="btn btn-primary" id="update" name="update">Update</button>
	        </div>
			
			<?php
			}
			?>
			
		</form> 
	</div>
	
	<!-- footer -->
	<?php include 'footer.php'; ?>
	
	
		<?php
		//form submission code
		if(isset($_POST['update'])){
				$hos_id = $_POST['hos_id'];
				$hos_rooms = $_POST['hos_rooms'];
				$hos_ppr = $_POST['hos_ppr'];
				$gender 	= $_POST['gender'];
				
				
				
				// Prepared statement to update hostel details
			    $update_sql = "UPDATE hostel SET hos_rooms = ?, students_per_room = ?, gender = ? WHERE hos_id = ?";
			    if ($stmt = mysqli_prepare($conn, $update_sql)) {
			        mysqli_stmt_bind_param($stmt, "iiis", $hos_rooms, $hos_ppr, $gender, $hos_id);
			        
			        if (mysqli_stmt_execute($stmt)) {
			            echo "<script>alert('Hostel details have been successfully updated!')</script>";
			            echo "<script>window.location = 'hostellist.php';</script>";
			        } else {
			            echo "<script>alert('Error updating hostel details. Please try again.')</script>";
			        }
			        
			        mysqli_stmt_close($stmt);
			    }
		}
		?>
	<!-- JavaScript -->    
    <script>
		var form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        })
	</script>
 </body>
</html>
