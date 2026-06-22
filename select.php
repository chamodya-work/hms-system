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
<div class="w-5/6" style="margin: 0 8rem;">

	<h2 class="text-center page-title"><i class="fa fa-group"></i> Review Hostel Applications</h2>
	<!--Form starts here-->
	<div style="display: flex;width: 100%;gap:1.5rem;">

		<form id="hoslist" action="" method="post" style="width: 80%;">
			<div class="form-row">

				<!-- academic year -->
				<div class="form-group col-md-2">
					<label for="acayr">Academic Year:</label>
					<?php
					$acayr = "SELECT * FROM academic_year WHERE is_current='1'";
					$acayr_sql = mysqli_query($conn, $acayr);
					$acayr_raw = mysqli_fetch_assoc($acayr_sql);
					$acayr_id = $acayr_raw['id'];
					$acayr_t = $acayr_raw['academic_year'];
					?>

					<input type="text" class="form-control" id="acayr_t" name="acayr_t" value="<?php echo $acayr_t; ?>"
						readonly>
					<input type="text" class="form-control" id="acayr" name="acayr" value="<?php echo $acayr_id; ?>"
						hidden>
				</div>

				<!-- course -->
				<div class="form-group col-md-2">
					<label for="course">Course:</label>
					<select class="form-control" id="course" name="course" onchange="submit()">
						<option value="">--Select Course--</option>
						<?php

						$course = "SELECT course FROM hostel_reg WHERE acayr = '$acayr_id' GROUP BY course ORDER BY course";
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

				if (isset($_POST['course']) AND ($_POST['course']) != null) {
					?>
					<!-- batch -->
					<div class="form-group col-md-2">
						<label for="batch">Batch:</label>
						<select class="form-control" id="batch" name="batch" onchange="submit()">
							<option value="">--Select Batch--</option>
							<?php

							$batch = "SELECT batch FROM hostel_reg WHERE acayr = '$acayr_id' AND course='" . $_POST['course'] . "' ORDER BY batch";
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
				if (isset($_POST['course']) && $_POST['course'] !== '' && isset($_POST['batch']) && $_POST['batch'] !== '') {
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
				if (!empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender'])) {
					?>
					<div class="form-group col-md-3">
						<label for="eligibilityFilter">Eligibility:</label>
						<select class="form-control" id="eligibilityFilter" name="eligibilityFilter" onchange="submit()">
							<option value="">All</option>
							<option value="not_reviewed" <?php if (isset($_POST['eligibilityFilter'])) {
								echo ($_POST['eligibilityFilter'] == "not_reviewed") ? 'selected' : '';
							} ?>>Not Reviewed</option>
							<option value="selected" <?php if (isset($_POST['eligibilityFilter'])) {
								echo ($_POST['eligibilityFilter'] == "selected") ? 'selected' : '';
							} ?>>Selected</option>
							<option value="not_selected" <?php if (isset($_POST['eligibilityFilter'])) {
								echo ($_POST['eligibilityFilter'] == "not_selected") ? 'selected' : '';
							} ?>>Not Selected</option>
							<option value="pending" <?php if (isset($_POST['eligibilityFilter'])) {
								echo ($_POST['eligibilityFilter'] == "pending") ? 'selected' : '';
							} ?>>Pending</option>
							<option value="rejected" <?php if (isset($_POST['eligibilityFilter'])) {
								echo ($_POST['eligibilityFilter'] == "rejected") ? 'selected' : '';
							} ?>>Rejected</option>

						</select>
					</div>

					<?php
				}
				?>
			</div>

			<?php
			if (!empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender'])) {
				?>
				<div class="form-row my-3">
					<div class="form-check-inline font-bold">Sort list by:</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filterOptions" id="filter1" value="income"
							onclick="submit()" <?php if (isset($_POST['filterOptions'])) {
								echo ($_POST['filterOptions'] == "income") ? 'checked' : '';
							} ?>>
						<label class="form-check-label" for="filter1">Income Status</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filterOptions" id="filter2" value="medical"
							onclick="submit()" <?php if (isset($_POST['filterOptions'])) {
								echo ($_POST['filterOptions'] == "medical") ? 'checked' : '';
							} ?>>
						<label class="form-check-label" for="filter2">Medical Status</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filterOptions" id="filter3" value="all"
							onclick="submit()" <?php if (isset($_POST['filterOptions'])) {
								echo ($_POST['filterOptions'] == "all") ? 'checked' : '';
							} ?>>
						<label class="form-check-label" for="filter3">Distance</label>
					</div>

				</div>

				<!-- Search by name -->
				<div class="form-group" style="">
					<input type="text" class="form-control w-50" id="searchInput" onkeyup="searchTable()"
						placeholder="Search by Student Number...">
				</div>
				<script>
					function searchTable() {
						var input, filter, table, tr, td, i, txtValue;
						input = document.getElementById("searchInput");
						filter = input.value.toUpperCase();
						table = document.getElementById("hoslist");
						tr = table.getElementsByTagName("tr");
						for (i = 0; i < tr.length; i++) {
							td = tr[i].getElementsByTagName("td")[0];
							if (td) {
								txtValue = td.textContent || td.innerText;
								if (txtValue.toUpperCase().indexOf(filter) > -1) {
									tr[i].style.display = "";
								} else {
									tr[i].style.display = "none";
								}
							}
						}
					}
				</script>

				<?php
				if (isset($_POST['course']) && $_POST['course'] == "SHS") {
					$_POST['course'] = "Bachelor of Science Honours in Speech and Language Therapy";
				} else if (isset($_POST['course']) && $_POST['course'] == "OT") {
					$_POST['course'] = "Bachelor of Science Honours in Occupational Therapy";
				}

				$hostel = "SELECT st.name AS stu_name,r.stureg_id, r.studentno, r.distance, (r.m_totincome + r.f_totincome + r.g_totincome) AS totincome, r.medical, r.med_cat, r.siblings, r.m_paysheet_tmp, r.f_paysheet_tmp, r.income_certificate_tmp, r.eligibility,r.eligibility_remark FROM registration r 
				INNER JOIN student_info st ON r.studentno = st.studentno
				WHERE r.applying_acayr = '$acayr_t' AND r.batch = '" . $_POST['batch'] . "' AND r.course = '" . $_POST['course'] . "' AND r.gender = '" . $_POST['gender'] . "' AND r.admit = '0' AND r.stureg_id = (SELECT MAX(stureg_id) FROM registration WHERE studentno = r.studentno AND applying_acayr = r.applying_acayr) ";

				if (isset($_POST['eligibilityFilter']) && $_POST['eligibilityFilter'] != "") {
					$hostel .= " AND r.eligibility = '" . $_POST['eligibilityFilter'] . "'";
				}

				//echo $hostel;
				if (isset($_POST['filterOptions'])) {

					switch ($_POST['filterOptions']) {
						case 'income':
							$hostel .= " ORDER BY totincome";
							break;
						case 'medical':
							$hostel .= " ORDER BY medical DESC";
							break;
						case 'all':
							$hostel .= " ORDER BY distance DESC";
							break;
						case 'eligibility':
							$hostel .= " ORDER BY eligibility DESC, distance DESC";
							break;
						default:
							break;
					}
				} else {

					$hostel .= " ORDER BY studentno";
				}

				$hostel_sql = mysqli_query($conn, $hostel);
				//echo $hostel;
				$rows = mysqli_num_rows($hostel_sql);
				if ($rows > 0) {
					?>

					<?php


					?>





					<script>
						function searchTable() {
							var input, filter, table, tr, td, i, txtValue;
							input = document.getElementById("searchInput");
							filter = input.value.toUpperCase();
							table = document.getElementById("hoslist");
							tr = table.getElementsByTagName("tr");
							for (i = 0; i < tr.length; i++) {
								td = tr[i].getElementsByTagName("td")[0];
								if (td) {
									txtValue = td.textContent || td.innerText;
									if (txtValue.toUpperCase().indexOf(filter) > -1) {
										tr[i].style.display = "";
									} else {
										tr[i].style.display = "none";
									}
								}
							}
						}
					</script>
					<div class="form-group mt-4">
						<table class="table table-hover" style="margin:auto;">
							<tbody>
								<tr>
									<th>Student No</th>
									<th>Name</th>
									<th>Distance</th>
									<th>Income (Rs.)</th>
									<th>Medical</th>
									<th>Siblings</th>
									<th>View Files</th>
									<!--<th>View Profile</th>-->
									<th>Eligibility</th>
									<th>Remark</th>
								</tr>
								<?php


								$i = 0;

								while ($hostel_raw = mysqli_fetch_assoc($hostel_sql)) {
									$stureg_id = $hostel_raw['stureg_id'];
									$stureg_name = $hostel_raw['stu_name'];
									$studentno = $hostel_raw['studentno'];
									$distance = $hostel_raw['distance'];
									$medical = $hostel_raw['medical'];
									$siblings = $hostel_raw['siblings'];
									$totincome = $hostel_raw['totincome'];
									$med_cat = $hostel_raw['med_cat'];
									$eligibility = $hostel_raw['eligibility'];
									$eligibility_remark = $hostel_raw['eligibility_remark'];
									$m_pay = $hostel_raw['m_paysheet_tmp'];
									$f_pay = $hostel_raw['f_paysheet_tmp'];
									$income_cert = $hostel_raw['income_certificate_tmp'];



									$medical = ($medical == 1) ? "Yes, " . $med_cat : "-";
									$siblings = ($siblings == 1) ? "Yes" : "-";
									$i++;
									?>

									<tr>

										<td><input type="text" name="<?php echo 'si' . $i ?>" value="<?php echo $stureg_id ?>"
												hidden><?php echo $studentno; ?></td>
										<td><?php echo $stureg_name; ?></td>
										<td><?php echo $distance; ?> km</td>
										<td style="text-align:right;"><?php
										echo ($totincome != null) ? number_format($totincome, 2) : "-";
										?></td>
										<td><?php echo $medical; ?></td>
										<td><?php echo $siblings; ?></td>
										<td>
											<?php
											if ($m_pay != null) {
												?>

												<a target="_blank"
													href="https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $m_pay ?>">Mother's
													paysheet</a><br>

												<?php
											}
											if ($f_pay != null) {
												?>

												<a target="_blank"
													href="https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $f_pay ?>">Father's
													paysheet</a><br>

												<?php
											}
											if ($income_cert != null) {
												?>

												<a target="_blank"
													href="https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $income_cert ?>">Garama
													Niladari Certificate</a>

												<?php
											}

											?>



										</td>
										<!--<td><a target="_blank" href="viewprofile.php?stureg_id=<?php //echo $stureg_id ?>"><i class="fa fa-address-card btn " style="background:green;color:white;padding:6px;" ></i></a></td>-->
										<!-- <td><input type="checkbox" value="1" id="eligibility<?php echo $i ?>" name="eligibility<?php echo $i ?>" <?php echo ($eligibility == 1) ? "checked" : ""; ?>></td> -->
										<td>
											<select class="form-control eligibility-dropdown" id="<?php echo 'eligibility' . $i ?>"
												name="<?php echo 'eligibility' . $i ?>">
												<!-- selected, not selected, pending, rejected -->
												<option value="not_reviewed" <?php echo ($eligibility == "not_reviewed" OR $eligibility == "") ? "selected" : ""; ?>>Not
													Reviewed</option>
												<option value="selected" <?php echo ($eligibility == "selected") ? "selected" : ""; ?>>Selected
												</option>
												<option value="not_selected" <?php echo ($eligibility == "not_selected") ? "selected" : ""; ?>>Not
													Selected</option>
												<option value="pending" <?php echo ($eligibility == "pending") ? "selected" : ""; ?>>
													Pending</option>
												<option value="rejected" <?php echo ($eligibility == "rejected") ? "selected" : ""; ?>>
													Rejected</option>
											</select>
										</td>

										<td>
											<textarea class="form-control" id="<?php echo 'remark' . $i ?>"
												name="<?php echo 'remark' . $i ?>" placeholder="Enter remarks here..." <?php
													 if ($eligibility == "not_reviewed") {
														 echo "";
													 } else {
														 echo "readonly";
													 }

													 ?>><?php echo $eligibility_remark; ?></textarea>
											<p style="text-align:right;cursor:pointer;" class="text-primary"
												id="<?php echo 'edit_remark' . $i ?>">Edit </p>
										</td>


									</tr>
									<?php
								}


								?>
							</tbody>
							<script>
								<?php
								for ($j = 1; $j <= $rows; $j++) {
									?>
									document.getElementById("<?php echo 'edit_remark' . $j ?>").addEventListener("click", function () {
										var remarkField = document.getElementById("<?php echo 'remark' . $j ?>");
										if (remarkField.hasAttribute("readonly")) {
											remarkField.removeAttribute("readonly");
											this.innerText = "Save";
										} else {
											remarkField.setAttribute("readonly", "readonly");
											this.innerText = "Edit";
										}
									});
									<?php
								}
								?>
							</script>
						</table>
					</div>
					<?php
				}

				?>

				<div class="form-group" style="text-align:right;">

					<button type="submit" class="btn btn-success" id="save" name="save">Save List
						<i class="fa fa-floppy-o" style="color:white;padding:6px;"> </i>
					</button>

				</div>
				<?php
			}

			?>
		</form>
		<div style="width: 20%;">
			<h5>Summary</h5>
			<?php
			if (isset($_POST['course']) && isset($_POST['batch']) && isset($_POST['gender'])) {
				$query = "SELECT COUNT(*) AS count, r.eligibility FROM registration r 
				INNER JOIN student_info st ON r.studentno = st.studentno
				WHERE r.applying_acayr = '$acayr_t' AND r.batch = '" . $_POST['batch'] . "' AND r.course = '" . $_POST['course'] . "' AND r.gender = '" . $_POST['gender'] . "' AND r.admit = '0' AND r.stureg_id = (SELECT MAX(stureg_id) FROM registration WHERE studentno = r.studentno AND applying_acayr = r.applying_acayr) 
				GROUP BY r.eligibility";

				$result = mysqli_query($conn, $query);
				$summary = [];
				while ($row = mysqli_fetch_assoc($result)) {
					$summary[$row['eligibility']] = $row['count'];

				}
				?>
				<table class="table table-bordered">
					<tbody>
						<tr>
							<td><b>Selected</b></td>
							<td id="count_selected">0</td>
						</tr>
						<tr>
							<td><b>Not Selected</b></td>
							<td id="count_not_selected">0</td>
						</tr>
						<tr>
							<td><b>Pending</b></td>
							<td id="count_pending">0</td>
						</tr>
						<tr>
							<td><b>Rejected</b></td>
							<td id="count_rejected">0</td>
						</tr>
						<tr>
							<td><b>Not Reviewed</b></td>
							<td id="count_not_reviewed">0</td>
						</tr>
					</tbody>
				</table>
				<form method="post">

					<button type="submit" class="btn btn-success w-full btn-warning text-white" style="width: 100%;"
						id="publish" name="publish">Publish List
						<i class="fa fa-cloud-upload" style="color:white;padding:6px;"> </i>
					</button>
				</form>
				<?php
			}
			?>

		</div>
	</div>

	<!--footer-->
</div>
</div>








<!-- footer -->
<?php include 'footer.php'; ?>

<?php
//form submission code
if (isset($_POST['save'])) {

	echo $rows;
	$save_sql = "";

	for ($i = 1; $i <= $rows; $i++) {
		$el = "eligibility" . $i;
		$eligibility = $_POST[$el];
		$si = "si" . $i;
		$stureg_id = $_POST[$si];
		$remark = "remark" . $i;
		$remark_text = mysqli_real_escape_string($conn, $_POST[$remark]);

		$save_sql .= "UPDATE registration SET eligibility='" . $eligibility . "', eligibility_remark='" . $remark_text . "' WHERE stureg_id='" . $stureg_id . "';";

	}

	$run_save = mysqli_multi_query($conn, $save_sql);
	if ($run_save) {
		echo "<script>alert('Your Hostel Student List has been saved successfully!')</script>";
		echo "<meta http-equiv='refresh' content='0'>";
	}
}
if (isset($_POST['publish'])) {
	require 'mail/gmail_api.php';
	// use safe fallbacks to avoid undefined index warnings
	$pub_course = isset($_POST['course']) ? $_POST['course'] : '';
	$pub_batch = isset($_POST['batch']) ? $_POST['batch'] : '';
	$pub_gender = isset($_POST['gender']) ? $_POST['gender'] : '';
	?>
	<script>
		if (confirm('Have you finalized and saved the list before proceeding?')) {
			<?php


			$hostel1 = "SELECT `email` FROM `registration` WHERE `eligibility` = '1' AND `applying_acayr` = '$acayr_id' AND `course` = '" . $pub_course . "' AND `batch` = '" . $pub_batch . "' AND `gender` = '" . $pub_gender . "' ";
			//echo "hos1".$hostel1;
			$hostel_sql1 = mysqli_query($conn, $hostel1);
			$rows1 = mysqli_num_rows($hostel_sql1);
			if ($rows1 > 0) {
				while ($hostel_raw1 = mysqli_fetch_assoc($hostel_sql1)) {
					$email1 .= $hostel_raw1['email'] . ",";
				}
				//api_sendMail("hostelmed@kln.ac.lk" ,"piumem@kln.ac.lk","Hostel Alerts",$email1);
				api_sendMail($email1, "", "Hostel Alerts", "You are eligible for hostel accommodation. Kindly proceed with the payment of the hostel fee amounting to Rs. 1,100.00. Please make the payment to the Shroff and upload your receipt through the Hostel Management System (HMS).");

			}

			$hostel2 = "SELECT `email` FROM `registration` WHERE `eligibility` = '0' AND `applying_acayr` = '$acayr_id' AND `course` = '" . $pub_course . "' AND `batch` = '" . $pub_batch . "' AND `gender` = '" . $pub_gender . "' ";

			$hostel_sql2 = mysqli_query($conn, $hostel2);
			$rows2 = mysqli_num_rows($hostel_sql2);
			if ($rows2 > 0) {
				while ($hostel_raw2 = mysqli_fetch_assoc($hostel_sql2)) {
					$email2 .= $hostel_raw2['email'] . ",";
				}

				//api_sendMail("hostelmed@kln.ac.lk" ,"piumem@kln.ac.lk","Hostel Alerts",$email2);
				api_sendMail($email2, "", "Hostel Alerts", "Sorry, You are not eligible for hostel accommodation. ");
			}



			?>
		} else {
			alert('Action cancelled!');
		}
	</script>

	<?php


}



?>

</body>
<script>
	function updateSummary() {
		let counts = {
			selected: 0,
			not_selected: 0,
			pending: 0,
			rejected: 0,
			not_reviewed: 0
		};

		// loop through all dropdowns
		document.querySelectorAll(".eligibility-dropdown").forEach(select => {
			let val = select.value;

			if (counts[val] !== undefined) {
				counts[val]++;
			}
		});

		// update UI
		document.getElementById("count_selected").innerText = counts.selected;
		document.getElementById("count_not_selected").innerText = counts.not_selected;
		document.getElementById("count_pending").innerText = counts.pending;
		document.getElementById("count_rejected").innerText = counts.rejected;
		document.getElementById("count_not_reviewed").innerText = counts.not_reviewed;
	}

	// attach event listeners
	document.querySelectorAll(".eligibility-dropdown").forEach(select => {
		select.addEventListener("change", function () {
			updateSummary();
		});
	});

	// run once on page load
	window.addEventListener("load", updateSummary);
</script>
</html>