<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}

include("connection/connect.php");
date_default_timezone_set("Asia/Colombo");

// =========================================================================
// 1. RESTORE STATE AFTER REDIRECT
// =========================================================================
// This must run BEFORE we check for POST submissions
if (isset($_SESSION['keep_post'])) {
	$_POST['acayr'] = $_SESSION['keep_post']['acayr'];
	$_POST['course'] = $_SESSION['keep_post']['course'];
	$_POST['batch'] = $_SESSION['keep_post']['batch'];
	$_POST['gender'] = $_SESSION['keep_post']['gender'];

	// Clear the session so it doesn't persist forever
	unset($_SESSION['keep_post']);
}

// =========================================================================
// 2. PROCESS FORM SUBMISSION
// =========================================================================
if (isset($_POST['reghos'])) {

	// FIX: Initialize the variable to prevent "Undefined variable" warning
	$add_sql1 = "";

	$bedno = "SELECT `bed_id` FROM `hostel_bed` WHERE `availability` = 1; ";
	$bedno_sql = mysqli_query($conn, $bedno);

	while ($bedno_raw = mysqli_fetch_assoc($bedno_sql)) {
		$bed_id = $bedno_raw['bed_id'];

		// FIX: Check if the key exists AND is not empty to prevent "Undefined array key" warning
		if (isset($_POST[$bed_id]) && $_POST[$bed_id] != '') {

			$stu = $_POST[$bed_id];
			$acayr_id = $_POST['acayr'];

			// FIX: Changed to INSERT. If you use UPDATE, it won't work on new allocations because the row doesn't exist yet.
			$add_sql1 .= "INSERT INTO `studreg_bed` (`stureg_id`, `bed_id`, `academic_year_id`) VALUES ('$stu', '$bed_id', '$acayr_id'); ";
			$add_sql1 .= "UPDATE `hostel_bed` SET `availability`='0' WHERE `bed_id`='$bed_id'; ";
			$add_sql1 .= "UPDATE `registration` SET `admit` = 0 WHERE `stureg_id` = '$stu'; ";
		}
	}

	// Only run the query if $add_sql1 is not empty
	if ($add_sql1 !== "") {
		$run_add = mysqli_multi_query($conn, $add_sql1);
		if ($run_add) {

			// Set success message for the javascript alert after reload
			$_SESSION['success_msg'] = "Hostel details have been successfully updated!";

			// Save the filter states to the session
			$_SESSION['keep_post'] = [
				'acayr' => $_POST['acayr'] ?? null,
				'course' => $_POST['course'] ?? null,
				'batch' => $_POST['batch'] ?? null,
				'gender' => $_POST['gender'] ?? null
			];

			// Redirect to clear POST data
			header("Location: " . $_SERVER['PHP_SELF']);
			exit();
		}
	}
}
?>



<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php'; ?>
<div class="container">
	<h2 class="text-center"><br>Room Admission</h2><br><br>

	<!--Form starts here-->
	<form id="addhostel" action="" method="post" class="main-form needs-validation" novalidate>

		<div class="form-row">

			<!-- academic year -->
			<div class="form-group col-md-3">
				<label for="acayr">Academic Year:</label>

				<select class="form-control" id="acayr" name="acayr" onchange="submit()">
					<option value="">--Select Academic Year--</option>
					<?php

					$acayr = "SELECT academic_year,id FROM academic_year";
					$acayr_sql = mysqli_query($conn, $acayr);
					while ($acayr_raw = mysqli_fetch_assoc($acayr_sql)) {
						$aacayr = $acayr_raw['id'];
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

			<?php
			if (isset($_POST['acayr']) AND ($_POST['acayr']) != null) {
				?>
				<!-- course -->
				<div class="form-group col-md-3">
					<label for="course">Course:</label>
					<select class="form-control" id="course" name="course" onchange="submit()">
						<option value="">--Select Course--</option>
						<?php

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
						?>


					</select>
				</div>
				<?php
			}
			if (($_POST['acayr']) != null AND isset($_POST['course']) AND ($_POST['course']) != null) {
				?>
				<!-- batch -->
				<div class="form-group col-md-3">
					<label for="batch">Batch:</label>
					<select class="form-control" id="batch" name="batch" onchange="submit()">
						<option value="">--Select Batch--</option>
						<?php

						$batch = "SELECT batch FROM hostel_reg WHERE acayr = (SELECT id FROM academic_year WHERE academic_year = '" . $_POST['acayr'] . "') AND course='" . $_POST['course'] . "' ORDER BY batch";
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
						?>


					</select>
				</div>


				<?php
			}
			if (($_POST['acayr']) != null AND ($_POST['course']) != null AND isset($_POST['batch']) AND ($_POST['batch']) != null) {
				?>
				<!-- gender -->
				<div class="form-group col-md-3">
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


		</div>

		<div class="form-row">
			<div class="form-group col-md-12">
				<?php

				if (($_POST['acayr']) != null AND ($_POST['course']) != null AND isset($_POST['batch']) AND ($_POST['batch']) != null AND ($_POST['gender']) != null) {



					?>
					<table class="table table-hover " style="width:100%; margin:0px;">
						<tbody>
							<tr>

								<th style="width:10%;">Hostel</th>
								<th>Rooms</th>



							</tr>
							<?php


							$hostel = "SELECT `hos_id`, hos_floors FROM `hostel` WHERE hos_id != '0' AND gender = '" . $_POST['gender'] . "'
							ORDER BY hos_id ASC";
							$hostel_sql = mysqli_query($conn, $hostel);
							$rowcount = mysqli_num_rows($hostel_sql);


							while ($hostel_raw = mysqli_fetch_assoc($hostel_sql)) {

								$hos_id = $hostel_raw['hos_id'];
								$floors = $hostel_raw['hos_floors'];

								?>

								<tr>

									<td>
										<input type="text" class="form-control" name="<?php echo $hos_id; ?>"
											value="<?php echo $hos_id; ?>" readonly>
									</td>


									<?php
									$a = 0;

									while ($a < $floors) {

										echo '<td><h6><b>Floor ' . $a . '</b></h6>';

										$beds = "SELECT
											r.room_no,
											r.reserved,
											r.remark,
											COUNT(b.bed_id) AS totbeds
										FROM
											hostel_room AS r
										LEFT JOIN
											hostel_bed AS b
										ON
											r.hos_id = b.hos_id
											AND r.floor_no = b.floor_no
											AND r.room_no = b.room_no
										WHERE r.hos_id = '$hos_id' AND r.floor_no = '$a'
										GROUP BY
											r.room_no, r.reserved;";

										$beds_sql = mysqli_query($conn, $beds);

										echo "<table>";
										$room = 1;
										while ($beds_raw = mysqli_fetch_assoc($beds_sql)) {

											$room_id = $beds_raw['room_no'];
											$reserved = $beds_raw['reserved'];
											$remark = $beds_raw['remark'];
											$beds = $beds_raw['totbeds'];





											?>

											<!---<div class="form-check form-check-inline">-->
										<tr>
											<td>
												<label class="form-check-label"><?php echo $room_id; ?></label>&nbsp;
											</td>
											<td>
												<?php
												if ($reserved == 0) {

													$bedno = "SELECT hb.bed_id, hb.availability, r.studentno, r.applying_acayr FROM hostel_bed hb LEFT JOIN registration r ON hb.bed_id = r.bed_id AND (r.applying_acayr = '" . $_POST['acayr'] . "' OR r.applying_acayr IS NULL) WHERE hb.hos_id = '$hos_id' AND hb.floor_no = '$a' AND hb.room_no = '$room_id';";


													//SELECT b.bed_id, b.availability, r.studentno, r.applying_acayr FROM hostel_bed b LEFT JOIN registration r ON b.bed_id = r.bed_id WHERE b.hos_id = '$hos_id' AND b.floor_no = '$a' AND b.room_no = '$room_id' AND (r.applying_acayr = '2023/2024' OR r.applying_acayr IS NULL);";
								
													$bedno_sql = mysqli_query($conn, $bedno);
													while ($bedno_raw = mysqli_fetch_assoc($bedno_sql)) {
														$bed_id = $bedno_raw['bed_id'];
														$availability = $bedno_raw['availability'];
														$stuno = $bedno_raw['studentno'];


														if ($availability == 1) {
															?>

															<select class="form-control" id="<?php echo $bed_id; ?>" name="<?php echo $bed_id; ?>"
																<?php echo ($availability == '0') ? 'disabled' : ''; ?>>
																<?php

																if ($availability != '1') {
																	echo "<option value=''>" . $stuno . "</option>";
																} else {
																	?>
																	<option value="">----</option>
																	<?php

																	$acayr = "SELECT `stureg_id`,`studentno`, `applying_acayr` FROM `registration` WHERE `applying_acayr` = '" . $_POST['acayr'] . "' AND `course` = '" . $_POST['course'] . "' AND `batch` = '" . $_POST['batch'] . "' AND `gender` = '" . $_POST['gender'] . "' AND `admit` = '0' ORDER BY studentno;";




																	// $acayr = "SELECT r.stureg_id, r.studentno, sb.academic_year_id FROM studreg_bed sb INNER JOIN registration r ON sb.stureg_id = r.stureg_id WHERE sb.academic_year_id = '".$_POST['acayr']."' AND r.course = '".$_POST['course']."' AND r.batch = '".$_POST['batch']."' AND r. gender = '".$_POST['gender']."' AND r.admit = '1';";
									


																	$acayr_sql = mysqli_query($conn, $acayr);

																	while ($acayr_raw = mysqli_fetch_assoc($acayr_sql)) {
																		$stureg_id = $acayr_raw['stureg_id'];
																		$studentno = $acayr_raw['studentno'];
																		$acayr = $acayr_raw['applying_acayr'];

																		?>
																		<option value="<?php echo $stureg_id; ?>" <?php if (isset($_POST['$bed_id'])) {
																			   echo ($_POST['$bed_id'] == $stureg_id) ? 'selected' : '';
																		   } ?>>
																			<?php echo $studentno; ?>
																		</option>

																		<?php
																	}
																}

																?>
															</select>
															<?php

														} else {

														   $course = $_POST['course'];

														   if($course=="Medicine"){
															 $course = "MED";
														   }

															$assigned = "SELECT r.studentno as studentno 
																FROM studreg_bed sb 
																INNER JOIN registration r 
																ON sb.stureg_id = r.stureg_id 
																WHERE sb.bed_id = '$bed_id' 
																AND sb.academic_year_id = (
																	SELECT id 
																	FROM academic_year 
																	WHERE academic_year = '" . $_POST['acayr'] . "' 
																	AND course = '" . $course . "'
																);";

															$assigned_sql = mysqli_query($conn, $assigned);
															$assigned_raw = mysqli_fetch_assoc($assigned_sql);

															$studentno = isset($assigned_raw["studentno"]) ? $assigned_raw["studentno"] : "";
															if ($studentno != ""){
															?>

															<input type="text" class="form-control" value="<?php echo $studentno ?>" disabled />

															<?php
															} else{
																?>
																<input type="text" class="form-control"  disabled />
																<?php
															}

														}
													}
												} else {
													echo $remark;
												}

												?>
											</td>
										</tr>
										<?php

										$room++;
										}
										?>






							</table>
							<!--</div>-->


							<input type="text" class="form-control" name="rooms<?php echo $hos_id . $a; ?>"
								value="<?php echo $room; ?>" hidden>

							<?php
							$a++;
							echo "</td>";
									}
									?>

						<?php

							}
							?>






					</tr>
					<?php

				}


				?>
				</tbody>
				</table>
				<script>


					function calculateSum(room, avb) {
						var sumDisplay = document.getElementById('sumDisplay');
						var a = parseInt(avb);
						if (room.checked) {
							sumDisplay.value = parseInt(sumDisplay.value || 0) + a;
						} else {
							sumDisplay.value = parseInt(sumDisplay.value || 0) - a;

						}
					}
				</script>
				<?php


				?>

			</div>


			<div class="form-group" style="text-align:center;">
				<button type="submit" class="btn btn-primary" id="reghos" name="reghos">Save</button>
			</div>



	</form>
	<!--footer-->
</div>
<!-- footer -->
<?php include 'footer.php'; ?>



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

	var today = new Date().toISOString().split('T')[0];
	document.getElementsByName("rego")[0].setAttribute('min', today);
	document.getElementsByName("regc")[0].setAttribute('min', today);
</script>
</body>

</html>