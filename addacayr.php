<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}

function sanitize_input($data)
{
	return htmlspecialchars(stripslashes(trim($data)));
}
?>


<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php'; ?>
<h2 class="text-center page-title"><i class="fa fa-calendar"></i> Add New Academic Year</h2>

<div class="container">


	<!--Form starts here-->
	<form id="addacademicyear" action="" method="post" class="needs-validation" novalidate>

		<!-- Input Row -->
		<div class="row justify-content-center mb-4">

			<!-- Academic Year -->
			<div class="col-md-4 col-lg-3">
				<label for="academic_year">Academic Year:</label>
				<input type="text" class="form-control" id="academic_year" name="academic_year"
					pattern="[0-9]{4}/[0-9]{4}" placeholder="2024/2025"
					value="<?php echo isset($_POST['academic_year']) ? htmlspecialchars($_POST['academic_year']) : ''; ?>"
					required>
				<div class="invalid-feedback">
					Please provide a valid academic year (e.g., 2024/2025).
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<label for="course">Course:</label>
				<select class="form-control" id="course" name="course" required>
					<option value="">Select Course</option>
					<option value="OT">OT</option>
					<option value="MED">MED</option>
					<option value="SHS">SHS</option>
				</select>
			</div>

			<!-- Start Date -->
			<div class="col-md-4 col-lg-3">
				<label for="start_date">Start Date:</label>
				<input type="date" class="form-control" id="start_date" name="start_date"
					value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>"
					required>
				<div class="invalid-feedback">
					Please select a start date.
				</div>
			</div>

			<!-- Submit Button -->
			<div class="col-md-4 col-lg-2">
				<label>&nbsp;</label>
				<button type="submit" class="btn btn-success btn-block" id="add_year" name="add_year">
					<i class="fa fa-plus-circle"></i> Add Year
				</button>
			</div>

		</div>

	</form>

	<!-- Display existing academic years (optional) -->
	<div class="row justify-content-center mt-5">
		<div class="col-lg-8 col-md-10">
			<h5 class="mb-3">Existing Academic Years:</h5>
			<div class="table-responsive">
				<table class="table  table-hover hostel-table">
					<thead class="thead-dark">
						<tr>
							<th scope="col" class="text-center">Academic Year</th>
							<th scope="col" class="text-center">Course</th>
							<th scope="col" class="text-center">Start Date</th>
							<th scope="col" class="text-center">Status</th>
							<th scope="col" class="text-center">Lastly Promoted On</th>
							<th scope="col" class="text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Fetch and display existing academic years
						$sql = "SELECT * FROM academic_year ORDER BY start_date DESC";
						$result = $conn->query($sql);

						if ($result && $result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {

								echo "<tr>";
								echo "<td class='text-center'>" . htmlspecialchars($row['academic_year']) . "</td>";
								echo "<td class='text-center'>" . htmlspecialchars($row['course']) . "</td>";
								echo "<td class='text-center'>" . date('d M Y', strtotime($row['start_date'])) . "</td>";
								echo "<td class='text-center'>";
								if ($row['is_current'] == 1) {
									echo "<span class='badge badge-success'>Current</span>";
								} else {
									echo "<span class='badge badge-secondary'>Past</span>";
								}
								echo "<td class='text-center'>";
								if ($row['is_promoted'] == 1) {
									echo date('d M Y h:i A', strtotime($row['promoted_on']));
								} else {
									echo "-";
								}
								echo "<td> 
										<button onclick='confirmPromote(" . $row['id'] . ")' class='btn text-white btn-sm btn-success'><i class='fa fa-up'></i> Promote</button>
										</td>";
								echo "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='3' class='text-center'>No academic years found.</td></tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>

<!-- footer -->
<?php include 'footer.php'; ?>

<?php
if (isset($_POST['add_year'])) {
	$academic_year = sanitize_input($_POST['academic_year']);
	$start_date = sanitize_input($_POST['start_date']);
	$course_id = sanitize_input($_POST['course']);

	// Validation
	if (!preg_match('/^[0-9]{4}\/[0-9]{4}$/', $academic_year)) {
		echo "<script>alert('Invalid academic year format. Use format: 2024/2025');</script>";
		exit;
	}

	// Validate that the years are consecutive
	$years = explode('/', $academic_year);
	if ((int) $years[1] - (int) $years[0] != 1) {
		echo "<script>alert('Academic year must be consecutive (e.g., 2024/2025).');</script>";
		exit;
	}

	// Validate date format
	$date = DateTime::createFromFormat('Y-m-d', $start_date);
	if (!$date || $date->format('Y-m-d') !== $start_date) {
		echo "<script>alert('Invalid date format.');</script>";
		exit;
	}


	echo $academic_year . " - " . $start_date . " - " . $course_id;

	// Check if academic year already exists
	$check_stmt = $conn->prepare("SELECT academic_year FROM academic_year WHERE academic_year = ? AND course = ?");
	$check_stmt->bind_param("ss", $academic_year, $course_id);
	$check_stmt->execute();
	$check_result = $check_stmt->get_result();

	if ($check_result->num_rows > 0) {
		echo "<script>alert('This academic year for the selected course already exists!');</script>";
	} else {
		// Set all existing years to not current
		$conn->query("UPDATE academic_year SET is_current = 0");

		// Insert new academic year
		$stmt = $conn->prepare("INSERT INTO academic_year (academic_year, start_date, is_current, course) VALUES (?, ?, 1, ?)");
		$stmt->bind_param("sss", $academic_year, $start_date, $course_id);

		if ($stmt->execute()) {
			echo "<script>alert('Academic year has been successfully added!');</script>";
			echo "<script>window.location='addacayr.php';</script>";
		} else {
			echo "<script>alert('Error: " . $stmt->error . "');</script>";
		}

		$stmt->close();
	}

	$check_stmt->close();
}
?>

<!-- JavaScript: Bootstrap validation -->
<script>
	var form = document.querySelector('.needs-validation');
	form.addEventListener('submit', function (event) {
		if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
		}
		form.classList.add('was-validated');
	}, false);

	// Auto-format academic year input
	document.getElementById('academic_year').addEventListener('input', function (e) {
		let value = e.target.value.replace(/[^0-9]/g, '');
		if (value.length > 4) {
			value = value.slice(0, 4) + '/' + value.slice(4, 8);
		}
		e.target.value = value;
	});

	function confirmPromote(id) {
		if (confirm('Are you sure you want to promote this academic year?')) {
			window.location.href = 'promoteacayr.php?id=' + id;
		}
	}




</script>
</body>

</html>