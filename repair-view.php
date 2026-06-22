<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}
// Language switch
if (isset($_GET['lang'])) {
	$_SESSION['lang'] = $_GET['lang'];
}

// Default language
if (!isset($_SESSION['lang'])) {
	$_SESSION['lang'] = 'en';
}


?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php
include 'header.php';
include 'categoryMapping.php';

?>
<div style="width: 70%;margin:auto;">
	<div style="margin-bottom: 30px;">
		<h2 class="text-center"><br>Maintenance & Repair</h2><br><br>
	</div>

	<div class="d-flex" style="gap:1rem;">
		<a href="?lang=si">සිංහල</a>
		<a href="?lang=en">English</a>
	</div>

	<?php
	//show maintenance
	
	if ($_SESSION["cat"] != '1') {
		$repair = "SELECT rep_id, req_date, cat,room_no,hos_id,floor_no,description, status FROM repairs LEFT JOIN hostel_bed ON repairs.bed_id = hostel_bed.bed_id  ORDER BY rep_id DESC ";
	}

	$repair_sql = mysqli_query($conn, $repair);
	if (mysqli_num_rows($repair_sql) > 0) {
		?>
		<form method="post" action="pdfprint.php">
			<div style="margin-top:2rem;">
				<div>
					<?php

					$html = '<table class="table table-striped">
            <thead>
                <tr>
                    <th>Request Date</th>
                    <th>Hostel</th>
                    <th>Room No</th>
                    <th>Category</th>
                    <th>Nature of the request</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

					while ($repair_raw = mysqli_fetch_assoc($repair_sql)) {
						$rid = $repair_raw['rep_id'];
						$req_date = $repair_raw['req_date'];
						$cat = $repair_raw['cat'];
						$description = $repair_raw['description'];
						$status = $repair_raw['status'];
						$lang = $_SESSION['lang'];
						$hostel = $repair_raw['hos_id'];
						$room = $repair_raw['room_no'];

						// Handle language
						$categoryText = ($lang == 'en') ? $cat : $complain[$cat];
						$statusText = ($lang == 'en') ? $status : $status_[$status];

						// Edit button
						$editBtn = '';
						if ($_SESSION['cat'] == '2' && $status != "Completed") {
							$editBtn = "<a href='repair.php?rid=$rid'>
                                <i class='fa fa-pencil-square-o btn' style='background:green;color:white;padding:6px;'></i>
                            </a>";
						}

						$html .= "
                <tr>
                    <td>$req_date</td>
                    <td>$hostel</td>
                    <td>$room</td>
                    <td>$categoryText</td>
                    <td>$description</td>
                    <td>";


						$html .= "<form method='post' action='assign_form.php'>";
						$html .= "<input type='hidden' name='rep_id' value='" . $repair_raw['rep_id'] . "'>";
						$html .= "<select name='status' class='form-control' onchange='this.form.submit()'>";

						$html .= "<option value='Pending' " . ($repair_raw['status'] == 'Pending' ? 'selected' : '') . " disabled>Pending</option>";
						$html .= "<option value='Informed' " . ($repair_raw['status'] == 'Informed' ? 'selected' : '') . " disabled>Informed</option>";
						$html .= "<option value='In Progress' " . ($repair_raw['status'] == 'In Progress' ? 'selected' : '') . " >In Progress</option>";
						$html .= "<option value='Completed' " . ($repair_raw['status'] == 'Completed' ? 'selected' : '') . " >Completed</option>";
						$html .= "<option value='Closed' " . ($repair_raw['status'] == 'Closed' ? 'selected' : '') . " disabled>Closed</option>";

						$html .= "</select>";

						$html .= "</form></td>
                    <td>$editBtn</td>
                </tr>
            ";
					}

					$html .= "</tbody></table>";

					echo $html;

					?>
				</div>
			</div>
			<textarea name="html" hidden><?php echo htmlspecialchars($html); ?></textarea>
			<button type="submit" name="export_pdf">Export to PDF</button>

		</form>

		<?php
	}


	?>


</div>
<!-- footer -->
<?php include 'footer.php'; ?>



</body>

</html>