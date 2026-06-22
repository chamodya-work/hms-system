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
<?php include 'header.php'; ?>
<div class="container">
	<h2 class="text-center"><br>Selected List</h2><br><br>


	<!--Form starts here-->
	<form id="hoslist" action="" method="post" class="main-form">
		<div class="form-row">

			<div class="col-md-3">
				<label for="acayr">Academic Year:</label>
				<select class="form-control" id="acayr" name="acayr" onchange="submit()">
					<option value="">--Select Academic Year--</option>
					<?php
					$acayr = "SELECT acayr,academic_year FROM hostel_reg hr INNER JOIN academic_year ay ON hr.acayr=ay.id WHERE acayr!='0' GROUP BY acayr ORDER BY acayr DESC";
					$acayr_sql = mysqli_query($conn, $acayr);
					while ($acayr_raw = mysqli_fetch_assoc($acayr_sql)) {
						$aacayr = $acayr_raw['acayr'];
						$academic_year = $acayr_raw['academic_year'];
						?>
						<option value="<?php echo $academic_year; ?>" <?php if (isset($_POST['acayr'])) {
							   echo ($_POST['acayr'] == $academic_year) ? 'selected' : '';
						   } ?>>
							<?php echo $academic_year; ?>
						</option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="col-md-3">
				<label for="course">Course:</label>
				<select class="form-control" id="course" name="course" onchange="submit()">
					<option value="">--Select Course--</option>
					<?php
					if (isset($_POST['acayr']) AND ($_POST['acayr']) != null) {
						$course = "SELECT course FROM hostel_reg WHERE acayr = (SELECT id FROM academic_year WHERE academic_year = '" . $_POST['acayr'] . "') GROUP BY course ORDER BY course";
						$course_sql = mysqli_query($conn, $course);
						while ($course_raw = mysqli_fetch_assoc($course_sql)) {
							$acourse = $course_raw['course'];
							?>
							<option value="<?php echo $acourse; ?>" <?php if (isset($_POST['course'])) {
								   echo ($_POST['course'] == $acourse) ? 'selected' : '';
							   } ?>>
								<?php echo $acourse; ?>
							</option>
							<?php
						}
					}
					?>
				</select>
			</div>
			<div class="col-md-2">
				<label for="batch">Batch:</label>
				<select class="form-control" id="batch" name="batch" onchange="submit()">
					<option value="">--Select Batch--</option>
					<?php
					if (isset($_POST['course']) AND ($_POST['course']) != null) {
						$batch = "SELECT batch FROM hostel_reg WHERE acayr = (SELECT id FROM academic_year WHERE academic_year = '" . $_POST['acayr'] . "') AND course = '" . $_POST['course'] . "' GROUP BY batch ORDER BY batch";
						$batch_sql = mysqli_query($conn, $batch);
						while ($batch_raw = mysqli_fetch_assoc($batch_sql)) {
							$abatch = $batch_raw['batch'];
							?>
							<option value="<?php echo $abatch; ?>" <?php if (isset($_POST['batch'])) {
								   echo ($_POST['batch'] == $abatch) ? 'selected' : '';
							   } ?>>
								<?php echo $abatch; ?>
							</option>
							<?php
						}
					}
					?>
				</select>
			</div>
			<?php
			if (($_POST['acayr']) != null AND ($_POST['course']) != null AND isset($_POST['batch']) AND ($_POST['batch']) != null) {
				?>
				<!-- gender -->
				<div class="form-group col-md-2">
					<label for="gender">Gender:</label>
					<select class="form-control" id="gender" name="gender" onchange="submit()">
						<option value="">--Select Gender--</option>
						<option value="m" <?php if (isset($_POST['gender'])) {
							echo ($_POST['gender'] == "m") ? 'selected' : '';
						} ?>>Male</option>
						<option value="f" <?php if (isset($_POST['gender'])) {
							echo ($_POST['gender'] == "f") ? 'selected' : '';
						} ?>>Female</option>
					</select>
				</div>
				<?php
			}

			?>
			<?php
			if (($_POST['acayr']) != null AND ($_POST['course']) != null AND isset($_POST['batch']) AND ($_POST['batch']) != null AND isset($_POST['gender']) AND ($_POST['gender']) != null) {
				?>
				<div class="form-group col-md-2">
					<label for="payment">Payment Status:</label>
					<select class="form-control" id="payment" name="payment" onchange="submit()">
						<option value="all"  <?php if (isset($_POST['payment'])) {
							echo ($_POST['payment'] == "all") ? 'selected' : '';
						} ?>>All</option>
						<option value="1" <?php if (isset($_POST['payment'])) {
							echo ($_POST['payment'] == "1") ? 'selected' : '';
						} ?>>Paid</option>
						<option value="0" <?php if (isset($_POST['payment'])) {
							echo ($_POST['payment'] == "0") ? 'selected' : '';
						} ?>>Unpaid</option>
					</select>
				</div>
				<?php
			}

			?>


		</div>

		<?php
		if (($_POST['acayr']) != null AND ($_POST['course']) != null AND ($_POST['batch']) != null AND ($_POST['gender']) != null AND isset($_POST['payment']) AND ($_POST['payment']) != null) {


			$hostel = "SELECT stureg_id, studentno, admit, payslip_tmp,payment FROM `registration`  WHERE applying_acayr = '" . $_POST['acayr'] . "' AND batch = '" . $_POST['batch'] . "' AND course = '" . $_POST['course'] . "' AND gender = '" . $_POST['gender'] . "' AND eligibility = 'selected'";

			if ($_POST['payment'] == "1") {
				$hostel .= " AND payment = '1'";
			} elseif ($_POST['payment'] == "0") {
				$hostel .= " AND payment ='0'";
			}



			$hostel_sql = mysqli_query($conn, $hostel);
			//echo $hostel;
			$rows = mysqli_num_rows($hostel_sql);
			if ($rows > 0) {
				?>

				<?php


				?>
				<div class="form-group">
					<table class="table table-hover mt-2" >
						<tbody>
							<tr>
								<th>Student No</th>
								<th>Payment Status</th>
								<th>Payment Slip</th>
								<th>Admit</th>
								<th>Action</th>
							</tr>
							<?php


							$i = 0;

							while ($hostel_raw = mysqli_fetch_assoc($hostel_sql)) {
								$stureg_id = $hostel_raw['stureg_id'];
								$studentno = $hostel_raw['studentno'];
								$admit = $hostel_raw['admit'];
								$payslip = $hostel_raw['payslip_tmp'];
								$payment = $hostel_raw['payment'];

								$i++;
								?>

								<tr>

									<td><input type="text" name="<?php echo 'si' . $i ?>" value="<?php echo $stureg_id ?>"
											hidden><?php echo $studentno; ?></td>
									
									<td>
										<?php
										if ($hostel_raw['payment'] == '1') {
											echo "<span class='badge badge-success'>Paid</span>";
										} else {
											echo "<span class='badge badge-danger'>Unpaid</span>";
										}
										?>
									</td>

									<td>
										<?php
										if ($payslip != null OR $payslip != "") {

											?>
											<a target='_blank'
												href='https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $payslip; ?>'>View Payslip</a>


											<?php
										}


										?>

									</td>
									<td><input type="checkbox" value="1" id="admit<?php echo $i ?>" name="admit<?php echo $i ?>"
											<?php echo ($admit == 1) ? "checked" : ""; ?>></td>
								
									<td>
										<button class="btn btn-success btn-sm "
										 <?php
	                                      if($payment == 1){
											echo "disabled";
										  }else{
											echo "";
										  }
										 ?>
										
										>Send Reminder Email
											<i class="fa fa-envelope" style="color:white;padding:6px;"> </i>
										</button>
									</td>
										</tr>
								<?php
							}


							?>
						</tbody>
					</table>
				</div>
				<?php
			}

			?>

			<div class="form-group" style="text-align:center;">
				<button type="submit" class="btn" style="background:#2F4F4F;color:white;padding:6px;" id="save"
					name="save">Save
					<i class="fa fa-floppy-o" style="color:white;padding:6px;"> </i></button>
			</div>
			<?php
		}

		?>
	</form>
	<!--footer-->
</div>
<!-- footer -->
<?php include 'footer.php'; ?>

<?php
//form submission code
if (isset($_POST['save'])) {

	for ($i = 1; $i <= $rows; $i++) {
		$ad = "admit" . $i;
		$admit = ($_POST[$ad] == 1) ? '1' : '0';
		$si = "si" . $i;
		$stureg_id = $_POST[$si];

		$save_sql .= "UPDATE registration SET admit='" . $admit . "' WHERE stureg_id='" . $stureg_id . "';";


	}

	$run_save = mysqli_multi_query($conn, $save_sql);
	if ($run_save) {
		echo "<script>alert('Your Hostel Student List has been saved successfully!')</script>";
		echo "<meta http-equiv='refresh' content='0'>";
	}
}

?>

</body>

</html>