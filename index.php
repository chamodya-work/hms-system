<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}

if (isset($_GET['submitted']) && $_GET['submitted'] == 1) {
	echo '<div class="alert alert-success" role="alert">
            Your Hostel Application has been successfully submitted!
          </div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['apply'])) {
		$_SESSION['allowed_apply'] = true;
		header('Location: apply.php');
		exit();
	} elseif (isset($_POST['hosreg'])) {
		$_SESSION['allowed_reg'] = true;
		header('Location: reg.php');
		exit();
	}
}
?>

<style>
	/* Enhanced buttons */
	.btn-dark {
		border-radius: 8px;
		font-weight: 500;
		transition: all 0.3s ease;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
	}

	.btn-dark:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
	}

	/* Enhanced title */
	.welcome-title {
		color: #1c1c1cff;
		font-weight: 700;
		text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
	}

	/* Enhanced table */
	.table thead th {
		background: linear-gradient(135deg, #656565ff 0%, #515151ff 100%);
		color: white;
		border: none;
		font-weight: 500;
	}

	.table-striped tbody tr:nth-of-type(odd) {
		background-color: rgba(115, 20, 85, 0.05);
	}

	.table {
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	}

	h5 {
		color: #202020ff;
		font-weight: 600;
		margin-bottom: 20px;
	}

	body {
		background: #f5f5f5;
		padding: 0;
		overflow-x: hidden;
	}

	.profile-card {
		background: white;
		border-radius: 12px;
		padding: 30px 20px;
		text-align: center;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		transition: all 0.3s ease;
		height: 100%;
		border-top: 4px solid #222222ff;
	}

	.profile-card:hover {
		transform: translateY(-8px);
		box-shadow: 0 8px 25px rgba(115, 20, 85, 0.2);
	}

	.profile-img {
		width: 120px;
		height: 120px;
		border-radius: 50%;
		object-fit: cover;
		border: 4px solid #242424ff;
		margin-bottom: 20px;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
	}

	.profile-name {
		color: #1e1e1eff;
		font-size: 1.15rem;
		font-weight: 700;
		margin-bottom: 5px;
	}

	.profile-title {
		color: #666;
		font-size: 0.95rem;
		font-weight: 500;
		margin-bottom: 20px;
		padding-bottom: 15px;
		border-bottom: 2px solid #e9ecef;
	}

	.profile-info {
		text-align: left;
		margin: 0 auto;
		max-width: 280px;
	}

	.info-item {
		display: flex;
		align-items: flex-start;
		margin-bottom: 12px;
		color: #444;
		line-height: 1.6;
	}

	.info-item i {
		color: #373737ff;
		margin-right: 12px;
		margin-top: 3px;
		min-width: 18px;
	}

	.info-item a {
		color: #252525ff;
		text-decoration: none;
		word-break: break-all;
	}

	.info-item a:hover {
		text-decoration: underline;
	}

	.section-title {
		text-align: center;
		color: #262526ff;
		font-weight: 600;
		font-size: 1.5rem;
		margin-bottom: 40px;
		text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
	}

	@media (max-width: 768px) {
		.profile-card {
			margin-bottom: 30px;
		}
	}
</style>

<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php';
if ($_SESSION["cat"] == "1") {
	include 'getData.php';

	if (isset($_SESSION['student_data'])) {
		$data = $_SESSION['student_data'];
		//get student information from central DB
		$batch = $data['data']['Originalbatch'];
		$course = $data['data']['course'];
		$stnm = $data['data']['StudentNumber'];
	}
}
?>



<div class="container">

	<div class="row align-items-center">
		<div class="col-lg-6 mb-4 mb-lg-0">
			<h1 class="welcome-title" style="font-size: 2.5rem; line-height: 1.4; margin-bottom: 20px;">
				WELCOME TO THE<br>HOSTEL MANAGEMENT SYSTEM
			</h1>
			<?php
			// List all the hostels assigned to the subwarden
			if ($_SESSION["cat"] == '2' && isset($_SESSION['warden_id'])) {
				$warden_id = '7';
				$sql = "SELECT wh.hos_id FROM warden_hostel wh 
				WHERE wh.warden_id = $warden_id";
				$result = $conn->query($sql);
				// Get into an array
				$assigned_hostels = [];
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$assigned_hostels[] = $row['hos_id'];
					}
				}
			}

			?>
			<p class="text-muted" style="font-size: 1.1rem;">Manage your hostel applications and requests efficiently
			</p>
		</div>

		<div class="col-lg-6">
			<div class="d-flex flex-column gap-2">
				<?php
				//for students
				if ($_SESSION['cat'] == '4') {
					echo "<a class='btn btn-dark btn-lg w-100' style='color:white;' href='assigned.php'>Assigned Work</a>";

				} elseif ($_SESSION["cat"] == '1') {
					//apply now (if batch and course matches with an opened registration)
					$regopen =
						"SELECT hr.*, ay.academic_year AS acayr 
					FROM `hostel_reg` hr 
					JOIN `academic_year` ay ON hr.acayr = ay.id
					WHERE hr.rego<=now() AND now()<=hr.regc AND hr.batch = '" . $batch . "';";
					$regopen_sql = mysqli_query($conn, $regopen);

					if (mysqli_num_rows($regopen_sql) > 0) {
						$regopen_raw = mysqli_fetch_assoc($regopen_sql);
						$_SESSION['acayr'] = $regopen_raw['acayr'];
						?>
						<form method='post'>
							<button type='submit' name='apply' class='btn btn-dark btn-lg w-100'
								style='color:white;margin-bottom: 8px;'>
								<?php
								$existQuery = "SELECT stureg_id FROM registration WHERE studentno='$stnm' AND applying_acayr='" . $_SESSION['acayr'] . "' ORDER BY regdate DESC LIMIT 1";
								$existResult = mysqli_query($conn, $existQuery);
								if (mysqli_num_rows($existResult) > 0) {
									echo "Edit Your Application";
								} else {
									echo "Apply Now";
								}
								?>



							</button>
						</form>
						<?php
					} else {
						echo "<p class='text-muted text-center mb-3'><em>No Hostel Applications Open</em></p>";
						$eligibility = $payment = $admit = $bed_id = '';

						$sql = "SELECT `applying_acayr`, `eligibility`, payment, `admit`, `bed_id` FROM `registration` WHERE applying_acayr = (SELECT `acayr` FROM `hostel_reg` ORDER BY hosreg_id DESC LIMIT 1) AND studentno='$stnm' ORDER BY `regdate` DESC LIMIT 1;";
						$sql_run = mysqli_query($conn, $sql);

						if (mysqli_num_rows($sql_run) > 0) {
							$sql_raw = mysqli_fetch_assoc($sql_run);
							$eligibility = $sql_raw['eligibility'];
							$payment = $sql_raw['payment'];
							$admit = $sql_raw['admit'];
							$bed_id = $sql_raw['bed_id'];
						}

						if ($eligibility == 'selected' AND $admit == '0' AND $payment == '0') {
							echo "<a class='btn btn-dark btn-lg w-100' style='color:white;' href='payments.php'>Upload Payment Receipt</a>";
						} elseif ($eligibility == 'selected' AND $admit == '0' AND $payment == '1') {
							echo "<p class='alert alert-info text-center'>Hostel Allocation is in Progress...</p>";
						} elseif ($admit == '1' AND ($bed_id == '0' OR $bed_id == null)) {
							echo "<a class='btn btn-dark btn-lg w-100' style='color:white;' href='reg.php'>Choose a Room</a>";
						} elseif ($admit == '1' AND ($bed_id != '0' OR $bed_id != null)) {
							$sql = "SELECT `hos_id`, `floor_no`, `room_no`, `bed_no` FROM `hostel_bed` WHERE `bed_id` = $bed_id";
							$sql_run = mysqli_query($conn, $sql);
							if (mysqli_num_rows($sql_run) > 0) {
								$sql_raw = mysqli_fetch_assoc($sql_run);
								$hos_id = $sql_raw['hos_id'];
								$floor_no = $sql_raw['floor_no'];
								$room_no = $sql_raw['room_no'];
								$bed_no = $sql_raw['bed_no'];
							}
							echo "<div class='alert alert-success'><strong>Your Bed:</strong> Hostel $hos_id - Floor $floor_no - Room $room_no - Bed $bed_no</div>";
							echo "<a class='btn btn-dark btn-lg w-100' style='color:white;' href='repair.php'>Request Maintenance/Repair</a>";
						}
					}

					$ifreg = "SELECT `stureg_id`, regdate FROM `registration` WHERE `studentno`='$stnm' AND `applying_acayr` ='2024/2025' ORDER by stureg_id DESC LIMIT 1";
					$ifreg_sql = mysqli_query($conn, $ifreg);

					if (mysqli_num_rows($ifreg_sql) == 0) {
						?>
						<form method='post'>
							<button type='submit' name='hosreg' class='btn btn-dark btn-lg w-100'
								style='color:white; margin-bottom: 8px;'>Record Current Hostel Details</button>
							<a href="repair.php" class="btn btn-dark btn-lg w-100" style="color:white;margin-bottom: 8px;">Request
								Maintenance/Repair</a>
							<a href="repair-student-view.php" class="btn btn-dark btn-lg w-100" style="color:white;">View My
								Maintenance/Repair Requests</a>
						</form>
						<?php
					} else {
						$getreg = mysqli_fetch_assoc($ifreg_sql);
						$lu = $getreg['regdate'];
						echo "<p class='text-muted text-center'>Last Applied: " . htmlspecialchars($lu) . "</p>";
					}
				}
				//for subwarden
				elseif ($_SESSION["cat"] == '3' || $_SESSION["cat"] == '2') {
					?>
					<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;"
						href="hostellist.php">Hostel Information</a>
					<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;" href="addacayr.php">Add
						New Academic Year</a>
					<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;"
						href="hosreglist.php">Hostel Applications Dates</a>
					<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;"
						href="viewstudent.php">View Student Information</a>
					<?php
					if ($_SESSION["cat"] == '3') {
						?>
						<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;" href="select.php">Review
							Hostel Applications</a>
						<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;" href="user.php">Manage
							User Accounts</a>
						<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;" href="warden.php">Manage
							Wardens</a>
						<?php
					}
					?>
					<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;" href="viewselect.php">View
						Selected List</a>
					<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;"
						href="viewcurrent.php">View Current Student List</a>
					<a class="btn btn-dark btn-lg w-100" style="color:white; margin-bottom: 8px;"
						href="select-room.php">Update Rooms</a>
					<a class="btn btn-dark btn-lg w-100" style="color:white;" href="repair-view.php">Maintenance/Repairs</a>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>






<?php

//show maintenance requests for students
if ($_SESSION["cat"] == '1') {
	$repair = "SELECT rep_id, req_date, cat, description, status FROM repairs WHERE stu_no = '$stnm'; ";
}
if ($_SESSION["cat"] == '2') {
	$assigned_hostels_str = implode(',', $assigned_hostels);

	if (!empty($assigned_hostels)) {
		// Escape each value for safety and wrap in quotes
		$escaped_hostels = array_map(function ($id) use ($conn) {
			return "'" . mysqli_real_escape_string($conn, $id) . "'";
		}, $assigned_hostels);

		// Join them into a comma-separated string
		$assigned_hostels_str = implode(',', $escaped_hostels);

		$repair = "SELECT r.rep_id, r.req_date, r.cat, r.description, r.status, hb.bed_id
							FROM repairs r
							INNER JOIN hostel_bed hb ON r.bed_id = hb.bed_id
							WHERE r.status = 'pending'
							AND hb.hos_id IN ($assigned_hostels_str)";
	} else {
		// No hostels assigned → return empty result
		$repair = "SELECT r.rep_id, r.req_date, r.cat, r.description, r.status, hb.bed_id
							FROM repairs r
							INNER JOIN hostel_bed hb ON r.bed_id = hb.bed_id
							WHERE 0"; // always false
	}
}
if ($_SESSION["cat"] == '3') {
	$repair = "SELECT rep_id, req_date, cat, description, status FROM repairs WHERE status = 'completed'; ";
}

if (!empty($repair)) {
	$repair_sql = mysqli_query($conn, $repair);
	if (mysqli_num_rows($repair_sql) > 0) {
		?>
		<hr>

		<div class="container">
			<h2 class="section-title">
				<i class="fa fa-cogs"></i> Hostel Maintenance Requests
			</h2>
			<div class="row">


				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th scope="col">Request Date</th>
								<th scope="col">Category</th>
								<th scope="col">Nature of the request</th>
								<th scope="col">Status</th>
								<?php
								if ($_SESSION["cat"] == '2') {
									echo "<th scope='col'>Action</th>";
								}
								?>
							</tr>
						</thead>
						<tbody>

							<?php


							while ($repair_raw = mysqli_fetch_assoc($repair_sql)) {
								$rid = $repair_raw['rep_id'];
								$req_date = $repair_raw['req_date'];
								$cat = $repair_raw['cat'];
								$description = $repair_raw['description'];
								$status = $repair_raw['status'];

								?>

								<tr>
									<td><?php echo htmlspecialchars($req_date); ?></td>
									<td><?php echo htmlspecialchars($cat); ?></td>
									<td><?php echo htmlspecialchars($description); ?></td>
									<td><?php echo htmlspecialchars($status); ?></td>
									<td>
										<?php
										if ($_SESSION["cat"] == '2') {
											echo "<a href='repair.php?rid=$rid'><i class='fa fa-pencil-square-o btn' style='background:green;color:white;padding:6px;border-radius:5px;'></i></a>";
										}
										?>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<?php
	}
}

if ($_SESSION["cat"] == '1' || $_SESSION["cat"] == '3') {
	?>
	<hr>

	<div class="container">
		<h2 class="section-title">
			<i class="fa fa-users"></i> Subwarden Contact Information
			<?php
			echo $_SESSION['supervisor_category'];
			?>
		</h2>

		<div class="row">
			<!-- Profile 1 -->
			<?php
			$query = "SELECT title,name, email, contact,image,warden_id FROM user_warden";
			$result = $conn->query($query);
			while ($row = $result->fetch_assoc()) {
				//Get allocated blocks for the warden
				$warden_id = $row['warden_id'];
				$allocatedQuery = "SELECT h.hos_id FROM warden_hostel wh INNER JOIN hostel h ON wh.hos_id=h.hos_id WHERE wh.warden_id=$warden_id";
				$allocatedResult = $conn->query($allocatedQuery);
				$allocatedBlocks = [];
				while ($block = $allocatedResult->fetch_assoc()) {
					$allocatedBlocks[] = $block['hos_id'];
				}
				$allocatedBlocksList = implode(", ", $allocatedBlocks);



				echo "<div class='col-md-4'>
				<div class='profile-card'>
					<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['title']) . " " . htmlspecialchars($row['name']) . "' class='profile-img'>
					<div class='profile-name'>" . htmlspecialchars($row['title']) . ". " . htmlspecialchars($row['name']) . "</div>
					<div class='profile-title'>Subwarden</div>
					<div class='profile-info'>
						<div class='info-item'>
							<i class='fa fa-phone'></i>
							<span>" . htmlspecialchars($row['contact']) . "</span>
						</div>
						<div class='info-item'>
							<i class='fa fa-envelope'></i>
							<a href='mailto:" . htmlspecialchars($row['email']) . "'>" . htmlspecialchars($row['email']) . "</a>
						</div>
						<div class='info-item'>
							<i class='fa fa-building'></i>
							<span>" . $allocatedBlocksList . "</span>
						</div>
					</div>
				</div>
			</div>";
			}

			?>

			<!-- Profile 2 -->
			<div class="col-md-4">
				<div class="profile-card">
					<img src="images/Mr Danajaya.jpg" alt="Mr. D.D.D. Withanage" class="profile-img">
					<div class="profile-name">Mr. D.D.D. Withanage</div>
					<div class="profile-title">Subwarden - Grade II</div>
					<div class="profile-info">
						<div class="info-item">
							<i class="fa fa-phone"></i>
							<span>070 565 8954</span>
						</div>
						<div class="info-item">
							<i class="fa fa-envelope"></i>
							<a href="mailto:dwithanage8821@gmail.com">dwithanage8821@gmail.com</a>
						</div>
						<div class="info-item">
							<i class="fa fa-building"></i>
							<span>B1, D3, D5, D6, D8, D12</span>
						</div>
					</div>
				</div>
			</div>


		</div>
	</div>
	<?php
}
?>
<!-- footer -->
<?php include 'footer.php'; ?>

<!-- JavaScript -->
<script src="js/jquery-3.3.1.slim.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>

</html>