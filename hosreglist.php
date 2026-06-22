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
	<?php include 'header.php'; ?>
	<div class="container" >
		<h2 class="text-center page-title"><i class="fa fa-calendar"></i> Hostel Applications Dates</h2>
		
		
		<!--Form starts here-->
		<form id="hoslist" action=""  method="post" class="main-form">
			<div class="form-row" style="align-items: flex-end; gap: 15px;">
				<div class="form-group" style="flex: 1; min-width: 300px; margin-bottom: 0;">
					<label for="academicYearFilter">Select Academic Year:</label>
					<?php
					// preserve selected value so dropdown doesn't reset after submit
					$selected_acayr = isset($_POST['acayr_filter']) ? $_POST['acayr_filter'] : '';
					?>
					<select id="academicYearFilter" name="acayr_filter" class="form-control" onchange="document.getElementById('hoslist').submit();">
						<option value="">-- Select Academic Year --</option>
						<?php
							$acYear_query = "SELECT id, academic_year FROM academic_year ORDER BY academic_year DESC";
							$acYear_result = mysqli_query($conn, $acYear_query);
							while($acYear_row = mysqli_fetch_assoc($acYear_result)) {
								$selected = ($acYear_row['id'] == $selected_acayr) ? 'selected' : '';
								echo "<option value='" . $acYear_row['id'] . "' $selected>" . $acYear_row['academic_year'] . "</option>";
							}
						?>
					</select>
				</div>
				<div class="form-group" style="margin-bottom: 0;">
					<a href="hostelreg.php" class="open-reg-btn">Open New Registration</a>
				</div>

			</div>
			<div class="form-group" >
				<?php
							// Use selected filter 
							$selected_acayr = isset($_POST['acayr_filter']) ? $_POST['acayr_filter'] : '';
							
							if($selected_acayr !== ''){
								$hostel = "SELECT * FROM hostel_reg WHERE acayr='$selected_acayr' AND hosreg_id!='0' ORDER BY regc DESC ";
								//echo $hostel;		
								$hostel_sql = mysqli_query($conn, $hostel);
								$rowcount = mysqli_num_rows($hostel_sql);
								if($rowcount > 0){
				?>
				
				&nbsp;		
				<table class="table table-light table-hover hostel-table" style="width:75%;margin:auto;">
					<tbody>
					<thead class="thead-dark">
					<tr>
						<th>Course</th>
						<th>Batch</th>
						<th>Registration Open</th>
						<th>Registration Close</th>
						<th>Edit</th>
					</tr>
					</thead>
					<?php
							while ($hostel_raw=mysqli_fetch_assoc($hostel_sql)) {
								$hosreg_id = $hostel_raw['hosreg_id'];
								$rego = $hostel_raw['rego'];
								$regc = $hostel_raw['regc'];
								$acayr = $hostel_raw['acayr'];
								$batch = $hostel_raw['batch'];
								$course = $hostel_raw['course'];

							?>
					<tr>
						<td><?php  echo $course;  ?></td>
						<td><?php  echo $batch;  ?></td>
						<td><?php  echo $rego;  ?></td>
						<td><?php  echo $regc;  ?></td>
						<td><a href="hostelregedit.php?hosreg_id=<?php echo $hosreg_id ?>"><i class="fa fa-pencil-square-o btn " style="background:green;color:white;padding:6px;"> </i></a></td>
					</tr>
					<?php 
							}
						?>
					</tbody>
				</table>
				<?php 
							
						}
						else echo "*No registrations available for the selected academic year!";
					}
					else {
						echo "*Please select an academic year to view registrations.";
					}

						?>
			</div>
		
					

			

			
		</form> 
	<!--footer-->	
	</div>
	<!-- footer -->
	<?php include 'footer.php'; ?>
	
	
		
	
 </body>
</html>
