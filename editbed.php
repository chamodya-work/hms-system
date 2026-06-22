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
	<?php 
	
		include 'header.php'; 

		// Initialize variables
		$stuno = $gender = "";

		// Check if student ID is provided in the URL
		if(isset($_GET['sid'])){
			$sid = $_GET['sid'];

			// Use prepared statements to fetch student information securely
			$sql_stuno = "SELECT `studentno`, `gender` FROM `registration` WHERE stureg_id = ?";
		    if ($stmt = mysqli_prepare($conn, $sql_stuno)) {
		        mysqli_stmt_bind_param($stmt, "i", $sid); // Bind the student ID as integer parameter
		        mysqli_stmt_execute($stmt);
		        mysqli_stmt_bind_result($stmt, $stuno, $gender);
		        mysqli_stmt_fetch($stmt);
		        mysqli_stmt_close($stmt);
		    }
		}
		
	
	?>
	<div class="container" >
		<div style="margin-bottom: 30px;">
		<h2 class="text-center"><br>Edit Student Bed - <?php echo htmlspecialchars($stuno); ?></h2><br><br>
		</div>
		
		
		<!--Form starts here-->
		<form id="apply"  method="post" class="main-form needs-validation"  novalidate>
			

			<div class="form-row">
				
				<!-- Select Hostel -->	
				<div class="form-group col-md-3">
					<label for="hos1">Select Your Hostel:</label>
					<select class="form-control" id="hos1" name="hos1" onchange="submit()" required>
						<option value="">--Select Hostel--</option>
						<?php
							// Use prepared statements to fetch hostels based on gender
	                        if ($stmt = mysqli_prepare($conn, "SELECT hos_id FROM hostel WHERE gender = ? ORDER BY hos_id")) {
	                            mysqli_stmt_bind_param($stmt, "s", $gender);
	                            mysqli_stmt_execute($stmt);
	                            mysqli_stmt_bind_result($stmt, $hos_id);
	                            while (mysqli_stmt_fetch($stmt)) {
	                                echo "<option value=\"$hos_id\" " . (isset($_POST['hos1']) && $_POST['hos1'] == $hos_id ? 'selected' : '') . ">$hos_id</option>";
	                            }
	                            mysqli_stmt_close($stmt);
	                        }
						?>
					</select>
				</div>
				<?php
					// If a hostel is selected, fetch and display floors
					if (isset($_POST['hos1']) && !empty($_POST['hos1'])) {
				?>

				<!-- Select Floor -->
				<div class="form-group col-md-3">
					<label for="floor1">Select Your Floor:</label>
					<select class="form-control" id="floor1" name="floor1" onchange="submit()" required>
						<option value="">--Select Floor--</option>
						<?php
							// Fetch floors for selected hostel using prepared statements
	                        if ($stmt = mysqli_prepare($conn, "SELECT `floor` FROM `hostel_floor` WHERE `hos_id` = ?")) {
	                            mysqli_stmt_bind_param($stmt, "s", $_POST['hos1']);
	                            mysqli_stmt_execute($stmt);
	                            mysqli_stmt_bind_result($stmt, $floor_id);
	                            while (mysqli_stmt_fetch($stmt)) {
	                                $floor_label = ($floor_id == 0) ? "Ground Floor" : "Floor $floor_id";
	                                echo "<option value=\"$floor_id\" " . (isset($_POST['floor1']) && $_POST['floor1'] == $floor_id ? 'selected' : '') . ">$floor_label</option>";
	                            }
	                            mysqli_stmt_close($stmt);
	                        }
						?>
					</select>
				</div>
				<?php
					// If a floor is selected, fetch and display rooms
					if(isset($_POST['floor1'])  AND $_POST['floor1']!=null){
				?>	

				<!-- Select Room -->
				<div class="form-group col-md-3">
					<label for="room1">Select Your Room:</label>
					<select class="form-control" id="room1" name="room1" onchange="submit()" required>
						<option value="">--Select Room--</option>
						<?php
							// Fetch rooms for selected hostel and floor using prepared statements
	                        if ($stmt = mysqli_prepare($conn, "SELECT LPAD(`room_no`, 2, '0') AS roomno FROM `hostel_bed` WHERE `hos_id` = ? AND `floor_no` = ? GROUP BY `room_no` ORDER BY `room_no`")) {
	                            mysqli_stmt_bind_param($stmt, "si", $_POST['hos1'], $_POST['floor1']);
	                            mysqli_stmt_execute($stmt);
	                            mysqli_stmt_bind_result($stmt, $room_id);
	                            while (mysqli_stmt_fetch($stmt)) {
	                                echo "<option value=\"$room_id\" " . (isset($_POST['room1']) && $_POST['room1'] == $room_id ? 'selected' : '') . ">$room_id</option>";
	                            }
	                            mysqli_stmt_close($stmt);
	                        }
						?>
					</select>
				</div>
				
				<?php
					// If a room is selected, fetch and display available beds
					if(isset($_POST['room1'])  AND $_POST['room1']!=null){
				?>	

				<!-- Select Bed -->
				<div class="form-group col-md-3">
					<label for="bed1">Select Your Bed No.:</label>
					<select class="form-control" id="bed1" name="bed1" onchange="submit()" required>
						<option value="">--Select Hostel--</option>
						<?php
							// Fetch available beds for selected room and floor using prepared statements
	                        if ($stmt = mysqli_prepare($conn, "SELECT `bed_no`, bed_id FROM `hostel_bed` WHERE `hos_id` = ? AND `floor_no` = ? AND `room_no` = ? AND `availability` = '1' ORDER BY `bed_no`")) {
	                            mysqli_stmt_bind_param($stmt, "sii", $_POST['hos1'], $_POST['floor1'], $_POST['room1']);
	                            mysqli_stmt_execute($stmt);
	                            mysqli_stmt_bind_result($stmt, $bed_no, $bed_id);
	                            while (mysqli_stmt_fetch($stmt)) {
	                                echo "<option value=\"$bed_id\" " . (isset($_POST['bed1']) && $_POST['bed1'] == $bed_id ? 'selected' : '') . ">$bed_no</option>";
	                            }
	                            mysqli_stmt_close($stmt);
	                        }
						?>
					</select>
				</div>	
				<?php
					}}}
				?>
			</div>
			
			<div class="form-group" >
				<button type="submit" class="btn btn-primary" id="register" name="register">Save</button>
			</div>
			
		</form> 
	
	</div>
	<!-- footer -->
	<?php include 'footer.php'; ?>
	
	
		<?php
		
		//form submission code
		if(isset($_POST['register'])){
				
				$bed_id =  $_POST['bed1'];
				
				
				// Use prepared statements to update the registration and hostel bed availability securely
			    $register_sql = "UPDATE registration SET bed_id = ? WHERE stureg_id = ?; UPDATE hostel_bed SET availability = '0' WHERE bed_id = ?;";
			    if ($stmt = mysqli_prepare($conn, $register_sql)) {
			        mysqli_stmt_bind_param($stmt, "iii", $bed_id, $sid, $bed_id);
			        if (mysqli_stmt_execute($stmt)) {
			            echo "<script>alert('Your record has been successfully updated!')</script>";
			            echo "<script>window.location = 'viewcurrent.php';</script>";
			        } else {
			            echo "<script>alert('Error updating record. Please try again.')</script>";
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
