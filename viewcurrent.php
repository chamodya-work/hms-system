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
	<?php 
		include 'header.php'; 

		if (isset($_GET['delete_id'])) {
			
				$id = intval($_GET['delete_id']);
				$sql = "SELECT bed_id FROM registration WHERE stureg_id = $id; ";
					$result = $conn->query($sql);
					//echo $sql;
					if ($result->num_rows > 0) {
						$row = $result->fetch_assoc();
						$current_bed_id = $row['bed_id'];

						// SQL query to update
						$sql2 = "UPDATE `registration` SET `bed_id`='0' WHERE stureg_id = $id;
									UPDATE `hostel_bed` SET `availability`='1' WHERE bed_id = $current_bed_id ";
						
						if ($conn->multi_query($sql2) === TRUE) {
							echo"<script>alert('Bed has been successfully removed!')</script>";
							echo"<script> window.location =  'viewcurrent.php' ; </script>     ";
						} else {
							echo "Error deleting record: " . $conn->error;
						}
					} else {
						echo "Error deleting record: " . $conn->error;
					}

		}
	


	?>
	<div class="container" >
		<h2 class="text-center"><br>Current Student List</h2><br><br>
		
		
				
		<!--Form starts here-->
		<form id="hoslist" action=""  method="post" class="main-form">
		<div class="form-row">
		<!-- filter -->	
		<div class="form-group col-md-3">
					<label for="filter">Filter By:</label>
					
					<select class="form-control" id="filter" name="filter" onchange="submit()">
						<option value="" <?php if(isset($_POST['filter'])){
												echo ($_POST['filter']==null) ? 'selected':''; } ?>>-- Select --</option>
						<option value="hos" <?php if(isset($_POST['filter'])){
												echo ($_POST['filter']=="hos") ? 'selected':''; } ?>>Hostel</option>
						<option value="other" <?php if(isset($_POST['filter'])){
												echo ($_POST['filter']=="other") ? 'selected':''; } ?>>Other</option>
					</select>
		</div>
		</div>		
			
		<div class="form-row">
				<?php
				if(isset($_POST['filter']) AND ($_POST['filter']!= null)){
				?>
				<!-- academic year -->	
				<div class="form-group col-md-3">
					<label for="acayr">Academic Year:</label>
					
					<select class="form-control" id="acayr" name="acayr" onchange="submit()">
						<option value="">--Select Academic Year--</option>
						<?php
						
							$acayr = "SELECT applying_acayr FROM registration GROUP BY applying_acayr ORDER BY applying_acayr DESC LIMIT 4";
							$acayr_sql = mysqli_query($conn, $acayr);
							while ( $acayr_raw=mysqli_fetch_assoc($acayr_sql)) { 
								$aacayr = $acayr_raw['applying_acayr'];
								
						?>
							  <option value="<?php echo $aacayr; ?>" <?php if(isset($_POST['acayr'])){
												echo ($_POST['acayr']==$aacayr) ? 'selected':''; } ?> > 
												<?php echo $aacayr; ?> </option>
							
						<?php	
							}
						?>
						
						
					</select>
				</div>
				
				<?php 
				if(isset($_POST['acayr']) AND ($_POST['acayr'])!=null AND $_POST['filter']=='other'){
					?>
				<!-- course -->	
				<div class="form-group col-md-3">
					<label for="course">Course:</label>
					<select class="form-control" id="course" name="course" onchange="submit()">
						<option value="">--Select Course--</option>
						<?php
						
							$course = "SELECT course FROM registration WHERE applying_acayr = '".$_POST['acayr']."' GROUP BY course ORDER BY course";
							$course_sql = mysqli_query($conn, $course);
							while ( $course_raw=mysqli_fetch_assoc($course_sql)) { 
								$acourse = $course_raw['course'];
							
								
						?>
							 <option value="<?php echo $acourse; ?>" <?php if(isset($_POST['course'])){
												echo ($_POST['course']==$acourse) ? 'selected':''; } ?> > 
												<?php echo $acourse; ?> </option>
							
						<?php	
							}
						?>
						
						
					</select>
				</div>
				<?php
				}
				if(($_POST['acayr'])!=null AND isset($_POST['course']) AND ($_POST['course'])!=null AND $_POST['filter']=='other'){
				?>
				<!-- batch -->	
				<div class="form-group col-md-3">
					<label for="batch">Batch:</label>
					<select class="form-control" id="batch" name="batch" onchange="submit()">
						<option value="">--Select Batch--</option>
						<?php
						
							$batch = "SELECT batch FROM registration WHERE applying_acayr = '".$_POST['acayr']."' AND course='".$_POST['course']."' GROUP BY batch ORDER BY batch DESC";
							$batch_sql = mysqli_query($conn, $batch);
							while ( $batch_raw=mysqli_fetch_assoc($batch_sql)) { 
								$abatch = $batch_raw['batch'];
							
								
						?>
							 <option value="<?php echo $abatch; ?>" <?php if(isset($_POST['batch'])){
												echo ($_POST['batch']==$abatch) ? 'selected':''; } ?> > 
												<?php echo $abatch; ?> </option>
							
						<?php	
							}
						?>
						
						
					</select>
				</div>
				
				
				<?php
				}
				if(($_POST['acayr'])!=null AND ($_POST['course'])!=null AND isset($_POST['batch']) AND ($_POST['batch'])!=null AND $_POST['filter']=='other'){
				?>
				<!-- gender -->	
				<div class="form-group col-md-3">
					<label for="gender">Gender:</label>
					<select class="form-control" id="gender" name="gender" onchange="submit()">
						<option value="">--Select Gender--</option>
						<option value="m" <?php if(isset($_POST['gender'])){
												echo ($_POST['gender']=="m") ? 'selected':''; } ?>>Male</option>
						<option value="f" <?php if(isset($_POST['gender'])){
												echo ($_POST['gender']=="f") ? 'selected':''; } ?>>Female</option>
					</select>
				</div>
				<?php
				}
			}
				?>
				
				
			
			<?php
				if(isset($_POST['filter']) AND $_POST['filter']=="hos" AND $_POST['acayr']!=null){
					?>
		<!-- hostel -->	
		<div class="form-group col-md-3">
					<label for="filter">Hostel:</label>
					<select class="form-control" id="hostel" name="hostel" onchange="submit()">
						<option value="">-- Select --</option>
						<?php
						
							$hostel = "SELECT hos_id FROM hostel WHERE hos_id!='0' ORDER BY hos_id  ";
							$hostel_sql = mysqli_query($conn, $hostel);
							while ( $hostel_raw=mysqli_fetch_assoc($hostel_sql)) { 
								$ahostel = $hostel_raw['hos_id'];
							
								
						?>
							 <option value="<?php echo $ahostel; ?>" <?php if(isset($_POST['hostel'])){
												echo ($_POST['hostel']==$ahostel) ? 'selected':''; } ?> > 
												<?php echo $ahostel; ?> </option>
							
						<?php	
							}
						?>
						
						
					</select>
				</div>
				<?php
}
				?>
			
			
				
				
			</div>
		<?php
			$filter = "";
			if(($_POST['acayr'])!=null ){
				$filter = "WHERE applying_acayr = '".$_POST['acayr']."' ORDER BY studentno";

			}
			if(($_POST['acayr'])!=null AND ($_POST['course'])!=null){
				$filter = "WHERE applying_acayr = '".$_POST['acayr']."' AND course = '".$_POST['course']."' ORDER BY studentno";

			}
			if(($_POST['acayr'])!=null AND ($_POST['course'])!=null AND ($_POST['batch'])!=null){
				$filter = "WHERE applying_acayr = '".$_POST['acayr']."' AND batch = '".$_POST['batch']."' AND course = '".$_POST['course']."' ORDER BY studentno";

			}
			if(($_POST['acayr'])!=null AND ($_POST['course'])!=null AND ($_POST['batch'])!=null AND ($_POST['gender'])!=null){
				$filter = "WHERE applying_acayr = '".$_POST['acayr']."' AND batch = '".$_POST['batch']."' AND course = '".$_POST['course']."' AND gender = '".$_POST['gender']."' ORDER BY studentno";

			}
			
			
			
			if(($_POST['hostel'])!=null ){
				$filter = "WHERE hos_id = '".$_POST['hostel']."' ORDER BY studentno";
				
			}

			if((isset($_POST['acayr']) AND ($_POST['acayr'])!=null) OR (isset($_POST['hostel']) AND ($_POST['hostel'])!=null) ){
		
				$hostel = "SELECT *	FROM current_list ".$filter;
				//echo $hostel;
				$hostel_sql = mysqli_query($conn, $hostel);
				
				$rows = mysqli_num_rows($hostel_sql);
				if($rows>0){
		?>	
		
			<?php
			
	
	?>	
		<div class="form-group" >
			<table class="table table-hover" style="width:75%;margin:auto;">
			<tbody>
				<tr>
					<th>Student No</th>
					<th>Hostel</th>
					<th>Bed No (Floor-Room-Bed)</th>
					<th></th>
				</tr>
				<?php
					
						
						$i=0;	
							
						while ( $hostel_raw=mysqli_fetch_assoc($hostel_sql)) {
							$stureg_id = $hostel_raw['stureg_id'];
							$studentno = $hostel_raw['studentno'];
							$hostel = $hostel_raw['hos_id'];
							if($hostel_raw['bed_no']!=null){
								$bed = "F".$hostel_raw['floor_no']."-R".$hostel_raw['room_no']."-B".$hostel_raw['bed_no'];
							}else { $bed = "Please assign a bed!"; }
							$i++;
				?>
			
				<tr>
					
					<td><?php  echo $studentno;  ?></td>
					<td><?php  echo $hostel;  ?></td>
					<td><?php  echo $bed;  ?></td>
					<td>
						<?php
							if($hostel==null OR $hostel==0){
						?>
								<a href="editbed.php?sid=<?php echo $stureg_id ?>"><i class="fa fa-pencil-square-o btn " style="background:green;color:white;padding:6px;"></i></a>
						<?php
							} else{
						?>
							<a href="?delete_id=<?php echo urlencode($stureg_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fa fa-minus-circle btn " style="background:red;color:white;padding:6px;"></i></a>
								
						<?php
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
		<?php
				
			
			}}
				?>
			
			
			
		</form> 
	<!--footer-->	
	</div>
	<!-- footer -->
	<?php include 'footer.php'; ?>
	
	
	
	
 </body>
</html>
