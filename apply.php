<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}
if ($_SESSION['allowed_apply'] != true) {
	header('Location: index.php');
	exit();

}

// require 'mail/gmail_api.php';

?>


<!doctype html>
<html lang="en">
<!-- header-->
<?php
include 'header.php';

?>
<div class="container">
	<h2 class="text-center page-title"><i class="fa fa-pencil-square-o"></i> Hostel Application Form -
		<?php echo $_SESSION['acayr']; ?>
	</h2>
	<?php

	$studentno = $_SESSION['student_data']['data']['StudentNumber'] ?? ''; // or wherever you store student ID
	$existing = [];

	if (!empty($studentno)) {
		$query = "SELECT * FROM registration WHERE studentno = '$studentno' AND applying_acayr = '" . $_SESSION['acayr'] . "' LIMIT 1";
		$result = mysqli_query($conn, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			$existing = mysqli_fetch_assoc($result);

		}
	}
	?>
	<!--<img src="images/p1.jpg" alt="profile image" class="border border-warning" style="float:right;width:120px;height:120px;">-->

	<div class="alert alert-primary" role="alert">
		<p class="mb-0"><i class="fa fa-info-circle"></i> Please fill out the following application form to apply for
			hostel accommodation. Make sure to provide accurate information and upload the necessary documents where
			applicable. Incomplete applications may not be considered.</p>
	</div>
	<div class="alert alert-info" role="alert">
		<strong>Note:</strong> If your personal information (such as name, address, contact details, course, batch) has
		missing or changed recently, please contact Dean's office to update it in the Student database before submitting
		this application.
	</div>
	<hr style="border-top: 2px solid #bbb; margin: 40px 0;">
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


		$distance = $existing['distance'] ?? '';

		$stureg_id = $existing['stureg_id'] ?? null; // existing registration ID if any
	}
	?>

	<!--Form starts here-->
	<form id="apply" action="apply.php" method="post" class="main-form needs-validation" enctype="multipart/form-data"
		novalidate>
		<div class="form-row">

			<!-- Student Number -->
			<div class="form-group col-md-3">
				<label style="font-weight: 600;" for="stnm">Student Number:</label>
				<input type="text" class="form-control" value="<?php echo $stnm ?>" name="stnm" readonly>
			</div>
			<!-- course -->
			<div class="form-group col-md-2">
				<label style="font-weight: 600;" for="course">Course:</label>
				<input type="text" class="form-control" value="<?php echo $course ?>" name="course" readonly>
			</div>
			<!-- batch -->
			<div class="form-group col-md-1">
				<label style="font-weight: 600;" for="course">Batch:</label>
				<input type="text" class="form-control" value="<?php echo $batch ?>" name="batch" readonly>
			</div>
			<!-- academic year -->
			<div class="form-group col-md-3">
				<label style="font-weight: 600;" for="aca_yr">Academic Year:</label>
				<input type="text" class="form-control" value="<?php echo $aca_yr ?>" name="aca_yr" readonly>
			</div>

			<!-- District -->
			<div class="form-group col-md-3">
				<label style="font-weight: 600;" for="district">District:</label>
				<input type="text" class="form-control" value="<?php echo $district ?>" name="district" readonly>
			</div>

		</div>

		<div class="form-row">
			<!-- full name -->
			<div class="form-group col-md-6">
				<label style="font-weight: 600;" for="fname">Full Name:</label>
				<input type="text" class="form-control" value="<?php echo $fname ?>" name="fname" readonly>
			</div>
			<!-- Name with initials -->
			<div class="form-group col-md-4">
				<label style="font-weight: 600;" for="sname">Name with Initials:</label>
				<input type="text" class="form-control" value="<?php echo $sname ?>" name="sname" readonly>
			</div>
			<!-- gender -->
			<div class="form-group col-md-2">
				<label style="font-weight: 600;" for="gender">Gender:</label>
				<input type="text" class="form-control" value="<?php echo $gender ?>" name="gender" readonly>
			</div>
		</div>

		<div class="form-row">

			<!-- email -->
			<div class="form-group col-md-6">
				<label style="font-weight: 600;" for="email">Email:</label>
				<input type="text" class="form-control" value="<?php echo $email ?>" name="email" readonly>
				<small class="form-text text-muted">
					If you need to change your email address, please contact the Dean's Office.
				</small>
			</div>

			<!-- address -->
			<div class="form-group col-md-6">
				<label style="font-weight: 600;" for="addr">Address:</label>
				<input type="text" class="form-control" value="<?php echo $addr ?>" name="addr" readonly>
			</div>

		</div>

		<div class="form-row">

			<!-- post ofc -->
			<div class="form-group col-md-6">
				<label style="font-weight: 600;" for="postofc">Nearest Post Office:</label>
				<input type="text" class="form-control" value="<?php echo $postofc ?>" name="postofc" readonly>
			</div>

			<!-- distance -->
			<div class="form-group col-md-6">
				<label style="font-weight: 600;" for="distance">Distance from the Faculty to the nearest post office
					(km):</label>
				<input type="number" class="form-control" name="distance" min="1" value="<?php echo $distance ?>"
					required>

			</div>


		</div>

		<label style="font-weight: 600;" for="tel1">Contact Number:</label>
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
		<small class="form-text text-muted">
			If you need to update your contact numbers, please contact the Dean's Office.
		</small>
		<hr>
		<div class="form-row">
			<!-- schol -->
			<div class="form-group col-md-3">
				<label for="schol" style="font-weight: bold;">Are you receiving any scholarship?</label>
				<select class="form-control" id="schol" name="schol" onchange="ed_scholfields(this)" required>
					<option value="No" <?= ($existing['scholarships'] ?? '') == 'No' ? 'selected' : '' ?>>No</option>
					<option value="Mahapola" <?= ($existing['scholarships'] ?? '') == 'Mahapola' ? 'selected' : '' ?>>Yes,
						Mahapola</option>
					<option value="Bursary" <?= ($existing['scholarships'] ?? '') == 'Bursary' ? 'selected' : '' ?>>Yes,
						Bursary</option>
					<option value="Other" <?= ($existing['scholarships'] ?? '') == 'Other' ? 'selected' : '' ?>>Other
					</option>
				</select>
			</div>


			<div class='form-group col-md-3 align-self-end'>
				<label for='scholother' id='scholotherl' hidden>If Other, Please Specify:</label>
				<input type='text' class='form-control' name='scholother' id='scholother' hidden
					value="<?php echo $existing['schol_other'] ?? ''; ?>">
			</div>

			<!-- account -->

			<div class='form-group col-md-6'>
				<label for='acnt' id='acntl' hidden>Account Number:</label>
				<input type='text' class='form-control' placeholder='Please enter your bank account number' name='acnt'
					id='acnt' hidden value="<?php echo $existing['account_number'] ?? ''; ?>">
			</div>

			<!-- script to enable/disable fields -->
			<script type="text/javascript">
				function ed_scholfields(schol) {
					var selectedValue = schol.options[schol.selectedIndex].value;
					var txtOther = document.getElementById("scholother");
					txtOther.hidden = selectedValue == "Other" ? false : true;
					txtOther = document.getElementById("scholotherl");
					txtOther.hidden = selectedValue == "Other" ? false : true;

					txtOther = document.getElementById("acnt");
					txtOther.hidden = selectedValue != "No" ? false : true;
					txtOther = document.getElementById("acntl");
					txtOther.hidden = selectedValue != "No" ? false : true;
				}
			</script>


		</div>
		<hr>



		<div class="form-row" id="income">
			<!-- parent/guardian info -->
			<p style="font-weight: bold;">Parents/Guardians Annual Income Status:</p>
			<table class="table table-bordered">
				<thead class="thead-dark">
					<tr>
						<th scope="col"></th>
						<th scope="col">Job</th>
						<th scope="col">Job Category</th>
						<th scope="col">Annual Salary</th>
						<th scope="col">Other Annual Incomes</th>
						<th scope="col">Total Annual Income</th>
						<th scope="col">Upload Pay-sheet</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row">Mother</th>
						<td><input type='text' class='form-control' name='m_job'
								value="<?php echo $existing['m_job'] ?? ''; ?>"></td>
						<td>
							<select class="form-control" id="m_jobcat" name="m_jobcat">
								<option value="" <?= ($existing['m_jobcat'] ?? '') == '' ? 'selected' : '' ?>>--select--
								</option>
								<option value="gov" <?= ($existing['m_jobcat'] ?? '') == 'gov' ? 'selected' : '' ?>>
									Government</option>
								<option value="nongov" <?= ($existing['m_jobcat'] ?? '') == 'nongov' ? 'selected' : '' ?>>
									Non-Gov.</option>
								<option value="self" <?= ($existing['m_jobcat'] ?? '') == 'self' ? 'selected' : '' ?>>
									Self-employed</option>
							</select>


						</td>
						<td><input type='text' class='form-control' name='m_salary' id='m_salary' pattern="^[0-9]*$"
								value="<?php echo $existing['m_salary'] ?? ''; ?>">
						</td>
						<td><input type='text' class='form-control' name='m_otherincome' id='m_otherincome'
								pattern="^[0-9]*$" value="<?php echo $existing['m_otherincome'] ?? ''; ?>"></td>
						<div class="invalid-feedback">Please fill out this field!</div>
						<td><input type='text' class='form-control' name='m_totincome' id='m_totincome' readonly></td>
						<td>
							<input class="form-control form-control-sm" id="m_paysheet" name="m_paysheet" type="file"
								accept=".pdf,.png,.jpg,.jpeg,image/png,image/jpeg,application/pdf">
						</td>
						<?php
						if (!empty($existing['m_paysheet'])) {
							echo "<small class='form-text text-muted'>Already uploaded: <a class='text-link' href='mail/tmp_files/{$existing['m_paysheet']}' target='_blank'>View Pay-sheet</a></small>";
						}
						?>
					</tr>
					<!-- calculation -->
					<script>
						$(document).ready(function () {
							$('#m_salary, #m_otherincome').on('input propertychange paste load', function () {
								$('#m_totincome').val(
									parseFloat($("#m_salary").val() != '' ? $("#m_salary").val() : 0) +
									parseFloat($("#m_otherincome").val() != '' ? $("#m_otherincome").val() : 0)
								);
							});
						});

					</script>




					<tr>
						<th scope="row">Father</th>
						<td><input type='text' class='form-control' name='f_job'
								value="<?php echo $existing['f_job'] ?? ''; ?>"></td>
						<td>
							<select class="form-control" id="f_jobcat" name="f_jobcat">
								<option value="" <?= ($existing['f_jobcat'] ?? '') == '' ? 'selected' : '' ?>>--select--
								</option>
								<option value="gov" <?= ($existing['f_jobcat'] ?? '') == 'gov' ? 'selected' : '' ?>>
									Government</option>
								<option value="nongov" <?= ($existing['f_jobcat'] ?? '') == 'nongov' ? 'selected' : '' ?>>
									Non-Gov.</option>
								<option value="self" <?= ($existing['f_jobcat'] ?? '') == 'self' ? 'selected' : '' ?>>
									Self-employed</option>
							</select>

						</td>
						<td><input type='text' class='form-control' name='f_salary' id='f_salary' pattern="^[0-9]*$"
								value="<?php echo $existing['f_salary'] ?? ''; ?>">
						</td>
						<td><input type='text' class='form-control' name='f_otherincome' id='f_otherincome'
								pattern="^[0-9]*$" value="<?php echo $existing['f_otherincome'] ?? ''; ?>"></td>
						<div class="invalid-feedback">Please fill out this field!</div>
						<td><input type='text' class='form-control' name='f_totincome' id='f_totincome' readonly></td>
						<td>
							<input class="form-control form-control-sm" id="f_paysheet" name="f_paysheet" type="file"
								accept=".pdf,.png,.jpg,.jpeg,image/png,image/jpeg,application/pdf">
							<?php
							if (!empty($existing['f_paysheet'])) {
								echo "<small class='form-text text-muted'>Already uploaded: <a href='mail/tmp_files/{$existing['f_paysheet']}' target='_blank'>View Pay-sheet</a></small>";
							}
							?>
						</td>
					</tr>
					<!-- calculation -->
					<script>
						$(document).ready(function () {
							$('#f_salary, #f_otherincome').on('input propertychange paste', function () {
								$('#f_totincome').val(
									parseFloat($("#f_salary").val() != '' ? $("#f_salary").val() : 0) +
									parseFloat($("#f_otherincome").val() != '' ? $("#f_otherincome").val() : 0)
								);
							});
						});


					</script>
					<tr>
						<td colspan="7">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="sameParent" checked>
								<label class="form-check-label" for="sameParent">
									Guardian is same as Parent
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">Guardian</th>
						<td><input type='text' class='form-control' name='g_job' id="g_job"></td>

						<td>
							<select class="form-control" id="g_jobcat" name="g_jobcat">
								<option value="" <?= ($existing['g_jobcat'] ?? '') == '' ? 'selected' : '' ?>>--select--
								</option>
								<option value="gov" <?= ($existing['g_jobcat'] ?? '') == 'gov' ? 'selected' : '' ?>>
									Government</option>
								<option value="nongov" <?= ($existing['g_jobcat'] ?? '') == 'nongov' ? 'selected' : '' ?>>
									Non-Gov.</option>
								<option value="self" <?= ($existing['g_jobcat'] ?? '') == 'self' ? 'selected' : '' ?>>
									Self-employed</option>
							</select>
						</td>

						<td><input type='text' class='form-control' name='g_salary' id='g_salary' pattern="^[0-9]*$"
								value="<?php echo $existing['g_salary'] ?? ''; ?>"></td>
						<td><input type='text' class='form-control' name='g_otherincome' id='g_otherincome'
								pattern="^[0-9]*$" value="<?php echo $existing['g_otherincome'] ?? ''; ?>"></td>
						<td><input type='text' class='form-control' name='g_totincome' id='g_totincome' readonly></td>
						<td>
							<input class="form-control form-control-sm" id="g_paysheet" name="g_paysheet" type="file"
								accept=".pdf,.png,.jpg,.jpeg,image/png,image/jpeg,application/pdf">
							<?php
							if (!empty($existing['g_paysheet'])) {
								echo "<small class='form-text text-muted'>Already uploaded: <a href='mail/tmp_files/{$existing['g_paysheet']}' target='_blank'>View Pay-sheet</a></small>";
							}
							?>
						</td>
					</tr>
					<!-- calculation -->
					<script>
						$(document).ready(function () {
							$('#g_salary, #g_otherincome').on('input propertychange paste', function () {
								$('#g_totincome').val(
									parseFloat($("#g_salary").val() != '' ? $("#g_salary").val() : 0) +
									parseFloat($("#g_otherincome").val() != '' ? $("#g_otherincome").val() : 0)
								);
							});
						});

					</script>
					</tr>
				</tbody>
			</table>
			<div class='form-group col-md-5'>
				<label style="font-weight: bold;" for='incomecertificate'>Uploade the Certificate from the Grama
					Niladhari</label>
				<input class="form-control form-control-sm" id="incomecertificate" name="incomecertificate" type="file"
					accept=".pdf,.png,.jpg,.jpeg,image/png,image/jpeg,application/pdf"
					<?= empty($existing['income_certificate']) ? 'required' : '' ?>>
				<?php
				if (!empty($existing['income_certificate'])) {
					echo "<small class='form-text text-muted'>Already uploaded: <a href='mail/tmp_files/{$existing['income_certificate']}' target='_blank'>View Certificate</a></small>";
				}
				?>
			</div>
		</div>

		<!-- Disable guardian fields when mother or father fields contain data -->
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const mFields = ['m_job', 'm_salary', 'm_otherincome'];
				const fFields = ['f_job', 'f_salary', 'f_otherincome'];
				const gFieldIds = ['g_job', 'g_jobcat', 'g_salary', 'g_otherincome', 'g_paysheet', 'g_totincome'];

				function getElem(id) {
					return document.getElementById(id) || document.getElementsByName(id)[0] || null;
				}

				function anyHasValue(list) {
					return list.some(function (id) {
						const el = getElem(id);
						if (!el) return false;
						if (el.type === 'checkbox' || el.type === 'radio') return el.checked;
						return (el.value || '').toString().trim() !== '';
					});
				}

				function updateGuardianState() {
					const disable = anyHasValue(mFields) || anyHasValue(fFields);
					gFieldIds.forEach(function (id) {
						const el = getElem(id);
						if (!el) return;
						el.disabled = disable;
						// clear values when disabling
						if (disable) {
							try {
								if (el.type === 'file') el.value = null; else el.value = '';
							} catch (e) { }
						}
					});
				}

				// attach listeners
				mFields.concat(fFields).forEach(function (id) {
					const el = getElem(id);
					if (el) el.addEventListener('input', updateGuardianState);
				});

				// initial check
				updateGuardianState();
			});
		</script>

		<hr>

		<!-- Emergency Contact Name. Contact number,relationship -->
		<p style="font-weight: bold;">Emergency Contact</p>
		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="emername" style="font-weight: 600;">Name:</label>
				<input type="text" class="form-control" name="emername" id="emername" required
					value="<?php echo $existing['e_contact_name'] ?? ''; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="emertel" style="font-weight: 600;">Contact Number:</label>
				<input type="text" class="form-control" name="emertel" id="emertel" required
					value="<?php echo $existing['e_contact_number'] ?? ''; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="emerrel" style="font-weight: 600;">Relationship:</label>
				<input type="text" class="form-control" name="emerrel" id="emerrel" required
					value="<?php echo $existing['e_contact_relation'] ?? ''; ?>">
			</div>
		</div>
		<hr>
		<div class="form-row">

			<span style="font-weight: bold;">Are you appying based on a medical reason?&nbsp;&nbsp;&nbsp;</span>


			<!-- check for reason -->
			<div class="form-check form-check-inline ">
				<input class="form-check-input" type="checkbox" id="medical" name="medical" value="1"
					onchange="showmedical()" <?= isset($existing['medical']) && $existing['medical'] === '1' ? 'checked' : '' ?>>
				<label class="form-check-label" for="medical">Yes</label>
			</div>



		</div>

		<?php
		if (isset($existing['medical']) && $existing['medical'] === '1') {
			echo "<script>document.addEventListener('DOMContentLoaded', function() { showmedical(); });</script>";
		}
		?>

		<!-- script to show income and medical fields -->
		<script type="text/javascript">
			function showmedical(medical) {
				var checkbox = document.getElementById('medical');
				var medical = document.getElementById("medicalreasons");
				medical.hidden = checkbox.checked ? false : true;



			}
		</script>


		<div class="form-row mt-2" id="medicalreasons" hidden>
			<!-- medical reason -->
			<div class="form-group col-md-3 ml-0">
				<label style="font-weight: 600;" for="med">Select your medical reason:</label>
				<select class="form-control" id="med" name="med" <!--onchange="ed_medfields(this)" --> required>
					<option value="">----</option>
					<option value="mental" <?= isset($existing['med_cat']) && $existing['med_cat'] === 'mental' ? 'selected' : '' ?>>Mental Disorder</option>
					<option value="physical" <?= isset($existing['med_cat']) && $existing['med_cat'] === 'physical' ? 'selected' : '' ?>>Physical Disorder</option>
					<option value="other" <?= isset($existing['med_cat']) && $existing['med_cat'] === 'other' ? 'selected' : '' ?>>Other</option>
				</select>
			</div>
			<div class='form-group col-md-6'>
				<label style="font-weight: 600;" for='meddesc' id='meddesc1'>Description if any:</label>
				<input type='text' class='form-control' name='meddesc' id='meddesc'
					value="<?php echo $existing['med_desc'] ?? ''; ?>">
			</div>

			<div class='form-group col-md-3'>
				<label style="font-weight: 600;" for='medfile'>Upload your medical:</label>
				<input class="form-control form-control-sm" id="medfile" name="medfile" type="file"
					accept=".pdf,.png,.jpg,.jpeg,image/png,image/jpeg,application/pdf">
				<?php
				if (!empty($existing['med_file'])) {
					echo "<small class='form-text text-muted'>Already uploaded: <a href='mail/tmp_files/{$existing['med_file']}' target='_blank'>View Medical Document</a></small>";
				}
				?>
			</div>
		</div>


		<hr>

		<div class="form-row">
			<span style="font-weight: bold;">Do you have any siblings attending to universities/ higher educational
				institues ?&nbsp;&nbsp;&nbsp;</span>
			<!-- check for reason -->
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" id="siblingsyes" name="siblings" value="1"
					onchange="showsiblings()" <?= isset($existing['siblings']) && $existing['siblings'] === '1' ? 'checked' : '' ?> required>
				<label class="form-check-label" for="siblingsyes">Yes</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" id="siblingsno" name="siblings" value="0"
					onchange="showsiblings()" <?= isset($existing['siblings']) && $existing['siblings'] === '0' ? 'checked' : '' ?> required>
				<label class="form-check-label" for="siblingsno">No</label>
			</div>



		</div>

		<!-- script to show income and medical fields -->
		<script type="text/javascript">
			function showsiblings(siblingsyes) {
				var checkbox = document.getElementById('siblingsyes');
				var siblings = document.getElementById("siblingsinfo");
				siblings.hidden = checkbox.checked ? false : true;



			}

		</script>

		<?php
		if (isset($existing['siblings']) && $existing['siblings'] === '1') {
			echo "<script>document.addEventListener('DOMContentLoaded', function() { showsiblings(); });</script>";
		}

		?>


		<?php
		//load sibling data if exists
		$sib_data = [];
		if (!empty($existing) && isset($existing['siblings']) && $existing['siblings'] === '1') {
			$query = "SELECT * FROM siblings WHERE stureg_id = '$stureg_id'";
			$result = mysqli_query($conn, $query);
			if ($result && mysqli_num_rows($result) > 0) {
				while ($row = mysqli_fetch_assoc($result)) {
					$sib_data[] = $row;
				}
			}
		}
		?>


		<div class="form-row mt-2" id="siblingsinfo" hidden>
			<!-- siblings info -->

			<table class="table table-bordered" id="sibtbl">
				<thead class="thead-dark">
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Age</th>
						<th scope="col">Name of the University</th>
						<th scope="col">Year of Study</th>
						<th scope="col">Info on Scholarships</th>

					</tr>
				</thead>
				<tbody>
					<?php
					//foreach in siblings data array sib_data
					if (isset($existing["siblings"]) && $existing["siblings"] === '1') {
						foreach ($sib_data as $index => $sibling) {
							echo "<tr>
								<td><input type='text' class='form-control' name='sibname" . ($index + 1) . "' value='" . $sibling['sib_name'] . "'></td>
								<td><input type='text' class='form-control' name='sibage" . ($index + 1) . "' size='3' value='" . $sibling['sib_age'] . "'></td>
								<td><input type='text' class='form-control' name='sibuni" . ($index + 1) . "' value='" . $sibling['sib_university'] . "'></td>
								<td><input type='number' min='1' max='5' class='form-control' name='sibyr" . ($index + 1) . "' size='3' value='" . $sibling['year'] . "'></td>
								<td><input type='text' class='form-control' name='sibschol" . ($index + 1) . "' value='" . $sibling['sib_scholarships'] . "'></td>
							</tr>";
						}

					}

					// render empty rows like count = 5 - existing siblings
					$existing_count = count($sib_data);
					for ($i = $existing_count + 1; $i <= 5; $i++) {
						echo "<tr>
							<td><input type='text' class='form-control' name='sibname" . $i . "'></td>
							<td><input type='text' class='form-control' name='sibage" . $i . "' size='3'></td>
							<td><input type='text' class='form-control' name='sibuni" . $i . "'></td>
							<td><input type='number' min='1' max='5' class='form-control' name='sibyr" . $i . "' size='3'></td>
							<td><input type='text' class='form-control' name='sibschol" . $i . "'></td>
						</tr>";
					}

					?>

				</tbody>
			</table>



		</div>
		<hr>
		<div class="form-group">
			<!-- consent -->
			<div class="form-check form-check-inline">
				<input class="form-check-input" style="margin:0 20px;" type="checkbox" id="consent" name="consent"
					value="1" onchange="showsubmit()">
				<label class="form-check-label" for="consent">
					<strong>Declaration:</strong> I declare that all the above mentioned details are correct to the best
					of my knowledge and I understand if any time the university found any guilt on submitting fake
					details or documents to prove my eligibility for hostel, the university has the authority to cancel
					my eligibility for hostel facility.
				</label>
			</div>

			<!-- script to show income and medical fields -->


		</div>
		<div class="form-group">
			<?php
			if (isset($existing['stureg_id'])) {
				echo '<button type="submit" class="btn btn-primary btn-lg btn-block" id="update_register" name="update_register" disabled
				style="margin: 30px 0; font-weight: 600;">Update Application</button>';
			} else {
				echo '<button type="submit" class="btn btn-primary btn-lg btn-block" id="register" name="register" disabled
				style="margin: 30px 0; font-weight: 600;">Apply Now</button>';
			}
			?>

		</div>


	</form>
	<!--footer-->
</div>
<!-- footer -->
<?php include 'footer.php'; ?>


<!-- New Submission Code -->
<?php
//form submission code
function handleUpload($fileField, $prefix)
{
	if (isset($_FILES[$fileField]) && $_FILES[$fileField]['error'] === UPLOAD_ERR_OK) {
		$fileName = $_FILES[$fileField]['tmp_name'];
		$originalFileName = $_FILES[$fileField]['name'];
		$fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
		$uniqueFileName = uniqid($prefix, true) . '.' . $fileExtension;
		$destination = 'mail/tmp_files/' . $uniqueFileName;
		if (move_uploaded_file($fileName, $destination)) {
			return $uniqueFileName;
		}
	}
	return '';
}


if (isset($_POST['register'])) {
	// Initialize uploaded file variables
	$m_paysheet_tmp = '';
	$f_paysheet_tmp = '';
	$g_paysheet_tmp = '';
	$income_certificate_tmp = '';
	$med_file_tmp = '';

	// Student info
	$studentno = $_POST['stnm'];
	$distance = isset($_POST['distance']) ? $_POST['distance'] : 0;

	// Scholarships
	if (isset($_POST['schol']) && $_POST['schol'] == "Other") {
		$scholarships = isset($_POST['scholother']) ? $_POST['scholother'] : '';
	} else {
		$scholarships = isset($_POST['schol']) ? $_POST['schol'] : '';
	}

	$shol_account = !empty($_POST['acnt']) ? $_POST['acnt'] : 0;

	// Parent incomes
	$m_job = isset($_POST['m_job']) ? $_POST['m_job'] : '';
	$m_jobcat = isset($_POST['m_jobcat']) ? $_POST['m_jobcat'] : '';
	$m_salary = !empty($_POST['m_salary']) ? $_POST['m_salary'] : 0;
	$m_otherincome = !empty($_POST['m_otherincome']) ? $_POST['m_otherincome'] : 0;
	$m_totincome = !empty($_POST['m_totincome']) ? $_POST['m_totincome'] : 0;

	$f_job = isset($_POST['f_job']) ? $_POST['f_job'] : '';
	$f_jobcat = isset($_POST['f_jobcat']) ? $_POST['f_jobcat'] : '';
	$f_salary = !empty($_POST['f_salary']) ? $_POST['f_salary'] : 0;
	$f_otherincome = !empty($_POST['f_otherincome']) ? $_POST['f_otherincome'] : 0;
	$f_totincome = !empty($_POST['f_totincome']) ? $_POST['f_totincome'] : 0;

	$g_job = isset($_POST['g_job']) ? $_POST['g_job'] : '';
	$g_jobcat = isset($_POST['g_jobcat']) ? $_POST['g_jobcat'] : '';
	$g_salary = !empty($_POST['g_salary']) ? $_POST['g_salary'] : 0;
	$g_otherincome = !empty($_POST['g_otherincome']) ? $_POST['g_otherincome'] : 0;
	$g_totincome = !empty($_POST['g_totincome']) ? $_POST['g_totincome'] : 0;

	// Handle file uploads


	$m_paysheet_tmp = handleUpload('m_paysheet', 'ps_m_');
	$f_paysheet_tmp = handleUpload('f_paysheet', 'ps_f_');
	$g_paysheet_tmp = handleUpload('g_paysheet', 'ps_g_');
	$income_certificate_tmp = handleUpload('incomecertificate', 'ic_');
	$med_file_tmp = handleUpload('medfile', 'med_');

	// Medical info
	$medical = !empty($_POST['medical']) ? 1 : 0;
	$med_cat = isset($_POST['med']) ? $_POST['med'] : '';
	$med_desc = isset($_POST['meddesc']) ? $_POST['meddesc'] : '';

	// Siblings info
	$siblings = isset($_POST['siblings']) ? $_POST['siblings'] : 0;

	// Emergency contact
	$e_contact_name = isset($_POST['emername']) ? $_POST['emername'] : '';
	$e_contact_tel = isset($_POST['emertel']) ? $_POST['emertel'] : '';
	$e_contact_rel = isset($_POST['emerrel']) ? $_POST['emerrel'] : '';

	$applyingyr = $_SESSION['acayr'];
	$batch = $_POST['batch'];
	$course = $_POST['course'];
	$email = $_POST['email'];
	$gender = $_POST['gender'];

	// Insert registration
	$register_sql = "INSERT INTO registration 
    (applying_acayr, batch, course, studentno, email, gender, distance, scholarships, shol_account,
     m_job, m_jobcat, m_salary, m_otherincome, m_totincome, m_paysheet,
     f_job, f_jobcat, f_salary, f_otherincome, f_totincome, f_paysheet,
     g_job, g_jobcat, g_salary, g_otherincome, g_totincome, g_paysheet,
     income_certificate, `medical`, `med_cat`, med_desc, med_file, siblings,
     admit, e_contact_name, e_contact_number, e_contact_relation)
     VALUES
     ('$applyingyr','$batch','$course','$studentno','$email','$gender','$distance',
     '$scholarships','$shol_account','$m_job','$m_jobcat','$m_salary','$m_otherincome','$m_totincome','$m_paysheet_tmp',
     '$f_job','$f_jobcat','$f_salary','$f_otherincome','$f_totincome','$f_paysheet_tmp',
     '$g_job','$g_jobcat','$g_salary','$g_otherincome','$g_totincome','$g_paysheet_tmp',
     '$income_certificate_tmp','$medical','$med_cat','$med_desc','$med_file_tmp','$siblings',
     '0','$e_contact_name','$e_contact_tel','$e_contact_rel')";

	$run_register = mysqli_query($conn, $register_sql);

	// Insert siblings info if any
	if ($run_register && $siblings == 1) {
		$stureg_id = mysqli_insert_id($conn);
		$registersib_sql = '';

		for ($i = 1; $i <= 4; $i++) {
			$sibname = isset($_POST["sibname$i"]) ? $_POST["sibname$i"] : '';
			$sibage = isset($_POST["sibage$i"]) ? $_POST["sibage$i"] : '';
			$sibuni = isset($_POST["sibuni$i"]) ? $_POST["sibuni$i"] : '';
			$sibyr = isset($_POST["sibyr$i"]) ? $_POST["sibyr$i"] : '';
			$sibschol = isset($_POST["sibschol$i"]) ? $_POST["sibschol$i"] : '';

			if (!empty($sibname) && !empty($sibage) && !empty($sibuni)) {
				$registersib_sql .= "INSERT INTO `siblings`(`stureg_id`, `sib_name`, `sib_age`, `sib_university`, `year`, `sib_scholarships`) 
                                     VALUES ('$stureg_id','$sibname','$sibage','$sibuni','$sibyr','$sibschol');";
			}
		}
		if ($registersib_sql) {
			mysqli_multi_query($conn, $registersib_sql);
		}
	}
	insert_student_info($conn);

	if ($run_register) {
		// api_sendMail($email, "piumem@kln.ac.lk", "Hostel Alerts", "Your hostel application has been successfully submitted!");
		echo "<script>alert('Your Hostel Application has been successfully submitted!')</script>";
		echo "<meta http-equiv='refresh' content='0'>";
		echo "<script> window.location =  'index.php' ; </script>";
	} else {
		echo "<script>alert('Your Hostel Application was not submitted! Please try again.')</script>";
	}
}

if (isset($_POST['update_register'])) {

	$m_paysheet_tmp = handleUpload('m_paysheet', 'ps_m_');
	$f_paysheet_tmp = handleUpload('f_paysheet', 'ps_f_');
	$g_paysheet_tmp = handleUpload('g_paysheet', 'ps_g_');
	$income_certificate_tmp = handleUpload('incomecertificate', 'ic_');
	$med_file_tmp = handleUpload('medfile', 'med_');


	$m_paysheet_to_update = '';
	$f_paysheet_to_update = '';
	$g_paysheet_to_update = '';
	$income_certificate_to_update = '';
	$med_file_to_update = '';

	if ($m_paysheet_tmp) {
		$m_paysheet_to_update = ", m_paysheet = '$m_paysheet_tmp'";
	}
	if ($f_paysheet_tmp) {
		$f_paysheet_to_update = ", f_paysheet = '$f_paysheet_tmp'";
	}
	if ($g_paysheet_tmp) {
		$g_paysheet_to_update = ", g_paysheet = '$g_paysheet_tmp'";
	}
	if ($income_certificate_tmp) {
		$income_certificate_to_update = ", income_certificate = '$income_certificate_tmp'";
	}
	if ($med_file_tmp) {
		$med_file_to_update = ", med_file = '$med_file_tmp'";
	}


	$studentno = $_POST['stnm'];
	$distance = isset($_POST['distance']) ? $_POST['distance'] : 0;

	// Scholarships
	if (isset($_POST['schol']) && $_POST['schol'] == "Other") {
		$scholarships = isset($_POST['scholother']) ? $_POST['scholother'] : '';
	} else {
		$scholarships = isset($_POST['schol']) ? $_POST['schol'] : '';
	}

	$shol_account = !empty($_POST['acnt']) ? $_POST['acnt'] : 0;

	// Parent incomes
	$m_job = isset($_POST['m_job']) ? $_POST['m_job'] : '';
	$m_jobcat = isset($_POST['m_jobcat']) ? $_POST['m_jobcat'] : '';
	$m_salary = !empty($_POST['m_salary']) ? $_POST['m_salary'] : 0;
	$m_otherincome = !empty($_POST['m_otherincome']) ? $_POST['m_otherincome'] : 0;
	$m_totincome = !empty($_POST['m_totincome']) ? $_POST['m_totincome'] : 0;

	$f_job = isset($_POST['f_job']) ? $_POST['f_job'] : '';
	$f_jobcat = isset($_POST['f_jobcat']) ? $_POST['f_jobcat'] : '';
	$f_salary = !empty($_POST['f_salary']) ? $_POST['f_salary'] : 0;
	$f_otherincome = !empty($_POST['f_otherincome']) ? $_POST['f_otherincome'] : 0;
	$f_totincome = !empty($_POST['f_totincome']) ? $_POST['f_totincome'] : 0;

	$g_job = isset($_POST['g_job']) ? $_POST['g_job'] : '';
	$g_jobcat = isset($_POST['g_jobcat']) ? $_POST['g_jobcat'] : '';
	$g_salary = !empty($_POST['g_salary']) ? $_POST['g_salary'] : 0;
	$g_otherincome = !empty($_POST['g_otherincome']) ? $_POST['g_otherincome'] : 0;
	$g_totincome = !empty($_POST['g_totincome']) ? $_POST['g_totincome'] : 0;

	$medical = !empty($_POST['medical']) ? 1 : 0;
	$med_cat = isset($_POST['med']) ? $_POST['med'] : '';
	$med_desc = isset($_POST['meddesc']) ? $_POST['meddesc'] : '';

	// Siblings info
	$siblings = isset($_POST['siblings']) ? $_POST['siblings'] : 0;

	// Emergency contact
	$e_contact_name = isset($_POST['emername']) ? $_POST['emername'] : '';
	$e_contact_tel = isset($_POST['emertel']) ? $_POST['emertel'] : '';
	$e_contact_rel = isset($_POST['emerrel']) ? $_POST['emerrel'] : '';

	$applyingyr = $_SESSION['acayr'];
	$batch = $_POST['batch'];
	$course = $_POST['course'];
	$email = $_POST['email'];
	$gender = $_POST['gender'];

	$query = "UPDATE registration SET distance='$distance', scholarships='$scholarships', 
	shol_account='$shol_account', m_job='$m_job', m_jobcat='$m_jobcat', m_salary='$m_salary', m_otherincome='$m_otherincome', 
	m_totincome='$m_totincome' $m_paysheet_to_update, f_job='$f_job', f_jobcat='$f_jobcat', f_salary='$f_salary', 
	f_otherincome='$f_otherincome', f_totincome='$f_totincome' $f_paysheet_to_update, g_job='$g_job', g_jobcat='$g_jobcat', 
	g_salary='$g_salary', g_otherincome='$g_otherincome', g_totincome='$g_totincome' $g_paysheet_to_update $income_certificate_to_update, 
	`medical` = '$medical', `med_cat` = '$med_cat', med_desc = '$med_desc' $med_file_to_update, 
	siblings = '$siblings', e_contact_name = '$e_contact_name', e_contact_number = '$e_contact_tel', 
	e_contact_relation = '$e_contact_rel' WHERE studentno = '$studentno' AND applying_acayr = '$applyingyr'";

	$result = mysqli_query($conn, $query);


	// Insert siblings info if any updates 
	if ($result && $siblings == 1) {
		$stureg_id = $existing['stureg_id'];
		// delete existing siblings
		mysqli_query($conn, "DELETE FROM siblings WHERE stureg_id = '$stureg_id'");

		// insert new siblings
		for ($i = 1; $i <= 4; $i++) {
			$sibname = isset($_POST["sibname$i"]) ? $_POST["sibname$i"] : '';
			$sibage = isset($_POST["sibage$i"]) ? $_POST["sibage$i"] : '';
			$sibuni = isset($_POST["sibuni$i"]) ? $_POST["sibuni$i"] : '';
			$sibyr = isset($_POST["sibyr$i"]) ? $_POST["sibyr$i"] : '';
			$sibschol = isset($_POST["sibschol$i"]) ? $_POST["sibschol$i"] : '';

			if (!empty($sibname) && !empty($sibage) && !empty($sibuni)) {
				mysqli_query($conn, "INSERT INTO `siblings`(`stureg_id`, `sib_name`, `sib_age`, `sib_university`, `year`, `sib_scholarships`) 
									 VALUES ('$stureg_id','$sibname','$sibage','$sibuni','$sibyr','$sibschol')");
			}
		}
	}

	insert_student_info($conn);


	if ($result) {
		echo "<script>alert('Your Hostel Application has been successfully updated!')</script>";
		echo "<meta http-equiv='refresh' content='0'>";
		echo "<script> window.location =  'index.php' ; </script>";
	} else {
		echo "<script>alert('Your Hostel Application was not updated! Please try again.')</script>";
	}



}


function insert_student_info($conn){
	$studentno = $_POST['stnm'];
	$name = $_POST['fname'];
	$tel1 = $_POST['tel1'];
	$tel2 = $_POST['tel2'];
	$contact = $tel1 . ($tel2 ? ", " . $tel2 : "");
	$email = $_POST['email'];
	$sql = "INSERT INTO student_info (studentno, name, contact, email) VALUES ('$studentno', '$name', '$contact', '$email')";

	// Check if student info already exists
	$check_sql = "SELECT * FROM student_info WHERE studentno = '$studentno'";
	$check_result = mysqli_query($conn, $check_sql);
	if (mysqli_num_rows($check_result) == 0) {
		$result = mysqli_query($conn, $sql);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}else{
		$updateSql = "UPDATE student_info SET name='$name', contact='$contact', email='$email' WHERE studentno='$studentno'";
		$result = mysqli_query($conn, $updateSql);
	}
}


?>

<script>
	var form = document.querySelector('.needs-validation');
	form.addEventListener('submit', function (event) {
		if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
		}
		form.classList.add('was-validated');
	})
	document.addEventListener("DOMContentLoaded", function () {

		const checkbox = document.getElementById("sameParent");

		const guardianFields = [
			"g_job",
			"g_jobcat",
			"g_salary",
			"g_otherincome",
			"g_totincome",
			"g_paysheet"
		];

		function toggleGuardian() {

			guardianFields.forEach(function (id) {
				let el = document.getElementById(id);

				if (checkbox.checked) {
					el.disabled = true;
					el.value = "";
				} else {
					el.disabled = false;
				}

			});

		}

		checkbox.addEventListener("change", toggleGuardian);

		toggleGuardian(); // run on page load
	});
	document.querySelectorAll("input[type='file']").forEach(function (input) {

		input.addEventListener("change", function () {

			const maxSize = 10 * 1024 * 1024; // 2MB

			if (this.files[0].size > maxSize) {
				alert("File size must be less than 2MB");
				this.value = "";
			}

		});

	});
	document.querySelectorAll('input[type="file"]').forEach(function (input) {

		input.addEventListener('change', function () {

			const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];
			const maxSize = 2 * 1024 * 1024; // 2MB

			const file = this.files[0];
			if (!file) return; // no file selected

			// Check type
			if (!allowedTypes.includes(file.type)) {
				alert("Invalid file type! Allowed: PDF, PNG, JPG, JPEG.");
				this.value = ""; // clear file input
				return;
			}

			// Check size
			if (file.size > maxSize) {
				alert("File too large! Max 2MB.");
				this.value = "";
				return;
			}
		});

	});
	function showsubmit() {
		var checkbox = document.getElementById('consent');
		var submit = document.getElementById("register");
		var update_submit = document.getElementById("update_register");
		if (submit){
			submit.disabled = checkbox.checked ? false : true;
		}
		if (update_submit){
			update_submit.disabled = checkbox.checked ? false : true;
		}
	}

</script>
</body>

</html>