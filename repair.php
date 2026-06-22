<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}

?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php';


?>
<div class="container">
	<div style="margin-bottom: 30px;">
		<h2 class="text-center"><br>Request for Maintenance & Repair </h2> <br><br>
	</div>

	<?php

	if ($_SESSION["cat"] == '1') {

		include 'getData.php';
		if (isset($_SESSION['student_data'])) {
			$data = $_SESSION['student_data'];
			//get student information from central DB
	
			$stnm = $data['data']['StudentNumber'];
		}

		$hos = "SELECT hb.hos_id, hb.floor_no, hb.room_no, r.bed_id
									FROM registration r
									JOIN hostel_bed hb
									ON hb.bed_id = r.bed_id
									WHERE r.studentno = '$stnm'
									ORDER BY r.stureg_id DESC LIMIT 1";
		$hos_sql = mysqli_query($conn, $hos);
		$hos_raw = mysqli_fetch_assoc($hos_sql);
		if ($hos_raw) {
			$hos_id = $hos_raw['hos_id'];
			$floor_id = $hos_raw['floor_no'];
			$room_id = $hos_raw['room_no'];
			$bed_id = $hos_raw['bed_id'];
			?>

			<!--Form starts here-->
			<form id="apply" action="" method="post" class="main-form needs-validation" novalidate
				enctype="multipart/form-data"
			>
				<div class="form" style="width: 50%;margin: auto;">

					<!-- Student Number -->
					<div class="form-group ">
						<label for="stnm">Student Number:</label>
						<input type="text" class="form-control" value="<?php echo $stnm ?>" name="stnm" readonly>
					</div>

					<!-- Hostel -->
					<div class="form-group ">
						<label>Hostel:</label>
						<input type="text" class="form-control" value="<?php echo $hos_id ?>" name="hos" readonly>

					</div>

					<!-- Floor -->
					<div class="form-group ">
						<label>Floor:</label>
						<input type="text" class="form-control" value="<?php echo $floor_id ?>" name="room" readonly>
					</div>

					<!-- Room -->
					<div class="form-group ">
						<label>Room:</label>
						<input type="text" class="form-control" value="<?php echo $room_id ?>" name="room" readonly>
					</div>

					<!-- Contact -->
					<div class="form-group ">
						<label for="contact">Contact Number:</label>
						<input type="text" class="form-control" name="contact">
					</div>

					<div class="form-group ">
						<label for="cat">Category of the request:</label>
						<select class="form-control" id="cat" name="cat" required>
							<option value="">--Select Category--</option>
							<option value="Civil">Civil</option>
							<option value="Electrical">Electrical</option>
							<option value="Health">Health</option>
							<option value="Landscape">Landscape</option>
							<option value="Cleaning">Cleaning</option>
						</select>
					</div>
					<div class="form-group ">
						<label for="nature">Nature of the request:</label>
						<select class="form-control" id="nature" name="nature" required>
							<option value="">--Select Category--</option>
							<option value="Civil">Civil</option>
							<option value="Electrical">Electrical</option>
							<option value="Health">Health</option>
							<option value="Landscape">Landscape</option>
							<option value="Cleaning">Cleaning</option>

						</select>
					</div>
					<div class="form-group ">
						<label for="description">Remarks</label>
						<textarea type="text" class="form-control" name="description"></textarea>

					</div>
					<div class="form-group ">
						<label for="file">Upload an image (optional):</label>
						<input type="file" class="form-control-file" id="file" name="file" accept="image/*">
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary" id="register" name="register">Send Request</button>
					</div>

				</div>
			
			</form>


			<?php
		}
	}

	if ($_SESSION["cat"] == '2') {
		if (isset($_GET['rid'])) {
			//get hostel details
			$rid = $_GET['rid'];
			$select = "SELECT r.* , hb.* FROM `repairs` r LEFT JOIN hostel_bed hb ON r.bed_id = hb.bed_id WHERE `rep_id` = '$rid'";
			$run_select = mysqli_query($conn, $select);
			if ($run_select) {
				$repair_raw = mysqli_fetch_assoc($run_select);
				$req_date = $repair_raw['req_date'];
				$stu_no = $repair_raw['stu_no'];
				$contact = $repair_raw['contact'];
				$cat = $repair_raw['cat'];
				$desc = $repair_raw['description'];
				$hos_id = $repair_raw['hos_id'];
				$floor_no = $repair_raw['floor_no'];
				$room_no = $repair_raw['room_no'];
				$status = $repair_raw['status'];
				$nature = $repair_raw['nature'];

				if ($status != "Pending") {
					$infodate = $repair_raw['info_date'];
					$infoto = $repair_raw['info_to'];
				}

				if ($status == "Completed") {
					$dtcomplete = $repair_raw['completed_date'];
					$obs = $repair_raw['observer'];
					$workers = $repair_raw['attended_workers'];
				}
				?>

				<!--Form starts here-->
				<form id="apply" action="" method="post" class="main-form needs-validation" novalidate>
					<div class="form-row">


						<!-- Student Number -->
						<div class="form-group col-md-3">
							<label for="stnm">Student Number:</label>
							<input type="text" class="form-control" value="<?php echo $stu_no ?>" name="stnm" readonly>
						</div>

						<!-- Hostel -->
						<div class="form-group col-md-1">
							<label>Hostel:</label>
							<input type="text" class="form-control" value="<?php echo $hos_id ?>" name="hos" readonly>

						</div>

						<!-- Floor -->
						<div class="form-group col-md-1">
							<label>Floor:</label>
							<input type="text" class="form-control" value="<?php echo $floor_no ?>" name="room" readonly>
						</div>

						<!-- Room -->
						<div class="form-group col-md-1">
							<label>Room:</label>
							<input type="text" class="form-control" value="<?php echo $room_no ?>" name="room" readonly>
						</div>

						<!-- Contact -->
						<div class="form-group col-md-3">
							<label for="contact">Contact Number:</label>
							<input type="text" class="form-control" value="<?php echo $contact ?>" name="contact" readonly>
						</div>

						<div class="form-group col-md-3">
							<label for="req_date">Request Date:</label>
							<input type="text" class="form-control" value="<?php echo $req_date ?>" name="req_date" readonly>
						</div>

					</div>
					<div class="form-row">

						<div class="form-group col-md-3">
							<label for="cat">Category of the request:</label>
							<input type="text" class="form-control" value="<?php echo $cat ?>" name="cat" readonly>
						</div>
						<div class="form-group col-md-9">
							<label for="nature">Nature of the request:</label>
							<input type="text" class="form-control" value="<?php echo $nature ?>" name="nature" readonly>
						</div>

					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="description">Description:</label>
							<textarea type="text" class="form-control"  name="description" readonly><?php echo $desc ?></textarea>
						</div>
					</div>

					<?php




					if ($status != "Completed") {
						?>
						<hr>
						<div class="form-row">

							<div class="form-group col-md-4">
								<label for="dt">Date Informed:</label>
								<input type="date" class="form-control" name="dt" <?php if ($status != 'Pending')
									echo 'value="' . $infodate . '" readonly'; ?>>
							</div>
							<div class="form-group col-md-8">
								<label for="infoto">Informed To:</label>
								<input type="text" class="form-control" name="infoto" <?php if ($status != 'Pending')
									echo 'value="' . $infoto . '" readonly'; ?>>
							</div>

						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary" id="addeval" name="addeval" <?php if ($status != 'Pending')
								echo 'hidden'; ?>>Save</button>
						</div>

						<?php
					}

					if ($status == "Informed") {
						?>
						<hr>
						<div class="form-row">

							<div class="form-group col-md-4">
								<label for="dtc">Date Completed:</label>
								<input type="date" class="form-control" name="dtc" <?php if ($status == 'Completed')
									echo 'value="' . $dtcomplete . '" readonly'; ?>>
							</div>
							<div class="form-group col-md-8">
								<label for="obs">Observed By:</label>
								<input type="text" class="form-control" name="obs" <?php if ($status == 'Completed')
									echo 'value="' . $obs . '" readonly'; ?>>
							</div>
							<div class="form-group col-md-12">
								<label for="workers">Attended Workers:</label>
								<textarea style="width: 100%; height: 100px;" id="workers" name="workers" <?php if ($status == 'Completed')
									echo 'readonly'; ?>>
									<?php if ($status == 'Completed')
										echo 'value="' . $workers . '" '; ?>
									</textarea>
							</div>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary" id="addobs" name="addobs" <?php if ($status == 'Completed')
								echo 'hidden'; ?>>Save</button>
						</div>

						<?php
					}

					?>

				</form>


				<?php
			}
		}
	}


	?>



</div>
<!-- footer -->
<?php include 'footer.php'; ?>


<?php
//repair request submission by student
if (isset($_POST['register'])) {


	$contact = $_POST['contact'];
	$cat = $_POST['cat'];
	$nature = $_POST['nature'];
	$desc = $_POST['description'];
	$dest_path = NULL;
	// handle file upload
	if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
		$fileTmpPath = $_FILES['file']['tmp_name'];
		$fileName = $_FILES['file']['name'];
		$fileSize = $_FILES['file']['size'];
		$fileType = $_FILES['file']['type'];
		$uploadFileDir = './images/';
		$dest_path = $uploadFileDir . $fileName;

		if (move_uploaded_file($fileTmpPath, $dest_path)) {
			echo "<script>alert('File is successfully uploaded.')</script>";
			// You can also save the file path to the database if needed
			// $filePathInDB = $dest_path;
		} else {
			echo "<script>alert('There was an error uploading the file.')</script>";
			// Handle the error as needed
		}
	}



	$register_sql = "INSERT INTO `repairs`(`req_date`, `stu_no`, `cat`, `contact`,`nature`, `description`, `status`, `bed_id`, `file_path`) VALUES (CURDATE(),'$stnm','$cat','$contact','$nature','$desc','Pending','$bed_id', '$dest_path'); ";

	//echo $register_sql;
	$run_register = mysqli_multi_query($conn, $register_sql);



	if ($run_register) {
		//require 'mail/gmail_api.php';
		//api_sendMail($email ,"piumem@kln.ac.lk","Hostel Alerts","Current hostel information has been recorded successfully!");
		echo "<script>alert('Your request has been successfully submitted!')</script>";
		//echo "<meta http-equiv='refresh' content='0'>";
		echo "<script> window.location =  'index.php' ; </script>     ";

	}
}

//repair evaluation submission by subwarden
if (isset($_POST['addeval'])) {


	$infodate = $_POST['dt'];
	$infoto = $_POST['infoto'];


	$register_sql = "UPDATE `repairs` SET `info_date`='$infodate',`info_to`='$infoto', `status`='Informed' WHERE `rep_id`='$rid'";


	//echo $register_sql;
	$run_register = mysqli_multi_query($conn, $register_sql);



	if ($run_register) {
		//require 'mail/gmail_api.php';
		//api_sendMail($email ,"piumem@kln.ac.lk","Hostel Alerts","Current hostel information has been recorded successfully!");
		echo "<script>alert('Repair status has been successfully updated!')</script>";
		//echo "<meta http-equiv='refresh' content='0'>";
		echo "<script> window.location =  'index.php' ; </script>     ";

	}
}


//repair evaluation submission by subwarden
if (isset($_POST['addobs'])) {


	$dtcomplete = $_POST['dtc'];
	$obs = $_POST['obs'];
	$workers = $_POST['workers'];


	$register_sql = "UPDATE `repairs` SET `completed_date`='$dtcomplete',`observer`='$obs',`attended_workers`='$workers', `status`='Completed' WHERE `rep_id`='$rid'";


	//echo $register_sql;
	$run_register = mysqli_multi_query($conn, $register_sql);



	if ($run_register) {
		//require 'mail/gmail_api.php';
		//api_sendMail($email ,"piumem@kln.ac.lk","Hostel Alerts","Current hostel information has been recorded successfully!");
		echo "<script>alert('Repair status has been successfully updated!')</script>";
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