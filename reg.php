<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}
if ($_SESSION['allowed_reg'] != true) {
	header('Location: index.php');
	exit();
}
?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php';


?>
<div class="container">
	<div style="margin-bottom: 30px;">
		<h2 class="text-center"><br>Hostel Application Form
			<img src="images/p1.jpg" alt="profile image" class="border border-warning"
				style="float:right;width:120px;height:120px;">
		</h2> <br><br>
	</div>

	<?php
	include 'getData.php';
	if (isset($_SESSION['student_data'])) {
		$data = $_SESSION['student_data'];
		//get student information from central DB
	
		$stnm = $data['data']['StudentNumber'];
		$course = $data['data']['course'];
		$batch = $data['data']['Originalbatch'];
		$aca_yr = $data['data']['intakeAcademicYear'];
		$district = $data['data']['homeDistrict'];
		$fname = $data['data']['fullName'];
		$sname = $data['data']['name'];
		$gender = $data['data']['studentSex'];
		$email = $data['data']['studentEmail'];
		$addr = $data['data']['permenentAddress'];
		$postofc = '';
		$tel1 = $data['data']['contactMobile'];
		$tel2 = $data['data']['contactHome'];

	}
	?>

	<!--Form starts here-->
	<form id="apply" action="reg.php" method="post" class="main-form needs-validation" novalidate>
		<div class="form-row">

			<!-- Student Number -->
			<div class="form-group col-md-3">
				<label for="stnm">Student Number:</label>
				<input type="text" class="form-control" value="<?php echo $stnm ?>" name="stnm" readonly>
			</div>
			<!-- course -->
			<div class="form-group col-md-2">
				<label for="course">Course:</label>
				<input type="text" class="form-control" value="<?php echo $course ?>" name="course" readonly>
			</div>
			<!-- batch -->
			<div class="form-group col-md-1">
				<label for="course">Batch:</label>
				<input type="text" class="form-control" value="<?php echo $batch ?>" name="batch" readonly>
			</div>
			<!-- academic year -->
			<div class="form-group col-md-3">
				<label for="aca_yr">Academic Year:</label>
				<input type="text" class="form-control" value="<?php echo $aca_yr ?>" name="aca_yr" readonly>
			</div>

			<!-- District -->
			<div class="form-group col-md-3">
				<label for="district">District:</label>
				<input type="text" class="form-control" value="<?php echo $district ?>" name="district" readonly>
			</div>

		</div>

		<div class="form-row">
			<!-- full name -->
			<div class="form-group col-md-6">
				<label for="fname">Full Name:</label>
				<input type="text" class="form-control" value="<?php echo $fname ?>" name="fname" readonly>
			</div>
			<!-- Name with initials -->
			<div class="form-group col-md-4">
				<label for="sname">Name with Initials:</label>
				<input type="text" class="form-control" value="<?php echo $sname ?>" name="sname" readonly>
			</div>
			<!-- gender -->
			<div class="form-group col-md-2">
				<label for="gender">Gender:</label>
				<input type="text" class="form-control" value="<?php echo $gender ?>" name="gender" readonly>
			</div>
		</div>

		<div class="form-row">

			<!-- email -->
			<div class="form-group col-md-6">
				<label for="email">Email:</label>
				<input type="text" class="form-control" value="<?php echo $email ?>" name="email" readonly>
			</div>

			<!-- address -->
			<div class="form-group col-md-6">
				<label for="addr">Address:</label>
				<input type="text" class="form-control" value="<?php echo $addr ?>" name="addr" readonly>
			</div>

		</div>



		<label for="tel1">Contact Number:</label>
		<div class=" input-group">
			<div class="input-group-prepend">
				<span class="input-group-text" id="basic-addon1">Mobile</span>
			</div>
			<input type="text" class="form-control" id="tel1" name="tel1" value="<?php echo $tel1 ?>"
				aria-describedby="basic-addon1" readonly>
			&nbsp;
			<div class="input-group-prepend">
				<span class="input-group-text" id="basic-addon2">Home</span>
			</div>
			<input type="text" class="form-control" id="tel2" name="tel2" value="<?php echo $tel2 ?>"
				aria-describedby="basic-addon2" readonly>
		</div>

		<hr>
		<div class="form-row">

			<!-- current room -->
			<div class="form-group col-md-3">
				<!-- hostel -->
				<label for="hos1">Select Your Hostel:</label>
				<select class="form-control" id="hos1" name="hos1" onchange="submit()" required>
					<option value="">--Select Hostel--</option>
					<?php
					$hos = "SELECT hos_id FROM hostel WHERE `gender`='$gender' AND hos_id != '0'  ORDER BY hos_id";
					$hos_sql = mysqli_query($conn, $hos);
					while ($hos_raw = mysqli_fetch_assoc($hos_sql)) {
						$hos_id = $hos_raw['hos_id'];

						?>
						<option value="<?php echo $hos_id; ?>" <?php if (isset($_POST['hos1'])) {
							   echo ($_POST['hos1'] == $hos_id) ? 'selected' : '';
						   } ?>>
							<?php echo $hos_id; ?>
						</option>
					<?php
					}
					?>
				</select>
			</div>
			<?php
			if (isset($_POST['hos1']) AND $_POST['hos1'] != null) {
				?>

				<!-- floor -->
				<div class="form-group col-md-3">
					<label for="floor1">Select Your Floor:</label>
					<select class="form-control" id="floor1" name="floor1" onchange="submit()" required>
						<option value="">--Select Floor--</option>
						<?php
						$floor = "SELECT `floor` FROM `hostel_floor` WHERE `hos_id`='" . $_POST['hos1'] . "'";
						$floor_sql = mysqli_query($conn, $floor);
						while ($floor_raw = mysqli_fetch_assoc($floor_sql)) {
							$floor_id = $floor_raw['floor'];
							if ($floor_id == 0) {
								$floor = "Ground Floor";
							} else {
								$floor = "Floor " . $floor_id;
							}


							?>
							<option value="<?php echo $floor_id; ?>" <?php if (isset($_POST['floor1'])) {
								   echo ($_POST['floor1'] == $floor_id) ? 'selected' : '';
							   } ?>>
								<?php echo $floor; ?>
							</option>
						<?php
						}
						?>
					</select>
				</div>
				<?php
				if (isset($_POST['floor1']) AND $_POST['floor1'] != null) {
					?>

					<!-- room -->
					<div class="form-group col-md-3">
						<label for="room1">Select Your Room:</label>
						<select class="form-control" id="room1" name="room1" onchange="submit()" required>
							<option value="">--Select Room--</option>
							<?php
							$room = "SELECT `room_no` FROM `hostel_bed` WHERE `hos_id`='" . $_POST['hos1'] . "' AND `floor_no`='" . $_POST['floor1'] . "' GROUP BY `room_no` ORDER BY `room_no`";
							$room_sql = mysqli_query($conn, $room);
							while ($room_raw = mysqli_fetch_assoc($room_sql)) {
								$room_id = $room_raw['room_no'];

								?>
								<option value="<?php echo $room_id; ?>" <?php if (isset($_POST['room1'])) {
									   echo ($_POST['room1'] == $room_id) ? 'selected' : '';
								   } ?>>
									<?php echo $room_id; ?>
								</option>
							<?php
							}
							?>
						</select>
					</div>
					<?php
					if (isset($_POST['room1']) AND $_POST['room1'] != null) {
						?>

						<!-- bed -->
						<div class="form-group col-md-3">
							<label for="bed1">Select Your Bed No.:</label>
							<select class="form-control" id="bed1" name="bed1" onchange="submit()" required>
								<option value="">--Select Hostel--</option>
								<?php
								$bed = "SELECT `bed_no`, bed_id  FROM `hostel_bed` WHERE `hos_id`='" . $_POST['hos1'] . "' AND `floor_no` = '" . $_POST['floor1'] . "' AND `room_no`='" . $_POST['room1'] . "' AND `availability`='1' ORDER BY `bed_no`";
								$bed_sql = mysqli_query($conn, $bed);
								while ($bed_raw = mysqli_fetch_assoc($bed_sql)) {
									$bed_id = $bed_raw['bed_id'];
									$bed_no = $bed_raw['bed_no'];

									?>
									<option value="<?php echo $bed_id; ?>" <?php if (isset($_POST['bed1'])) {
										   echo ($_POST['bed1'] == $bed_id) ? 'selected' : '';
									   } ?>>
										<?php echo $bed_no; ?>
									</option>
								<?php
								}
								?>
							</select>
						</div>
						<?php
					}
				}
			}
			?>
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary" id="register" name="register">Save</button>
		</div>

	</form>
	<!--footer-->
</div>
<!-- footer -->
<?php include 'footer.php'; ?>


<?php
//form submission code
if (isset($_POST['register'])) {

	//$batch = $_POST['batch'];
	$studentno = $_POST['stnm'];
	$applyingyr = "2023/2024";
	$bed_id = $_POST['bed1'];
	$gender = $_POST['gender'];

	$register_sql = "INSERT INTO registration (applying_acayr, batch, course, studentno,email, bed_id, gender, admit) VALUES ('$applyingyr','$batch', '$course', '$studentno','$email', '$bed_id','$gender', '1'); UPDATE `hostel_bed` SET `availability`='0' WHERE `bed_id`='" . $bed_id . "';";

	//echo $register_sql;
	$run_register = mysqli_multi_query($conn, $register_sql);



	if ($run_register) {
		require 'mail/gmail_api.php';
		api_sendMail($email, "piumem@kln.ac.lk", "Hostel Alerts", "Current hostel information has been recorded successfully!");
		echo "<script>alert('Your Record has been recorded successfully!')</script>";
		//echo "<meta http-equiv='refresh' content='0'>";
		echo "<script> window.location =  'index.php' ; </script>     ";

	}
}
?>
<!-- JavaScript -->
<script>
	var form = document.querySelector('.needs-validation');
	form.addEventListener('submit', function (event) {
		if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
		}
		form.classList.add('was-validated');
	})
</script>
</body>

</html>