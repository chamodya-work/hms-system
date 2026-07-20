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
		<h2 class="text-center"><br>Hostel Applications List</h2><br><br>
		
				
		<!--Form starts here-->
		<form id="hoslist" action=""  method="post" class="main-form">
		<div class="form-row">
			
				<!-- academic year -->	
				<div class="form-group col-md-3">
					<label for="acayr">Academic Year:</label>
					
					<select class="form-control" id="acayr" name="acayr" onchange="submit()">
						<option value="">--Select Academic Year--</option>
						<?php
						
							$acayr = "SELECT acayr FROM hostel_reg WHERE acayr!='0' GROUP BY acayr ORDER BY acayr DESC";
							$acayr_sql = mysqli_query($conn, $acayr);
							while ( $acayr_raw=mysqli_fetch_assoc($acayr_sql)) { 
								$aacayr = $acayr_raw['acayr'];
								
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
				if(isset($_POST['acayr']) AND ($_POST['acayr'])!=null){
					?>
				<!-- course -->	
				<div class="form-group col-md-3">
					<label for="course">Course:</label>
					<select class="form-control" id="course" name="course" onchange="submit()">
						<option value="">--Select Course--</option>
						<?php
						
							$course = "SELECT course FROM hostel_reg WHERE acayr = '".$_POST['acayr']."' GROUP BY course ORDER BY course";
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
				if(($_POST['acayr'])!=null AND isset($_POST['course']) AND ($_POST['course'])!=null){
				?>
				<!-- batch -->	
				<div class="form-group col-md-3">
					<label for="batch">Batch:</label>
					<select class="form-control" id="batch" name="batch" onchange="submit()">
						<option value="">--Select Batch--</option>
						<?php
						
							$batch = "SELECT batch FROM hostel_reg WHERE acayr = '".$_POST['acayr']."' AND course='".$_POST['course']."' ORDER BY batch";
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
				if(($_POST['acayr'])!=null AND ($_POST['course'])!=null AND isset($_POST['batch']) AND ($_POST['batch'])!=null){
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
				
				?>
				
				
			</div>
			
		<?php
			if(($_POST['acayr'])!=null AND ($_POST['course'])!=null AND ($_POST['batch'])!=null AND ($_POST['gender'])!=null){
		?>		
			<div class="form-row" style="margin-bottom:20px">
				<div class="form-check-inline">Sort list by:</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="filterOptions" id="filter1" value="income" onclick="submit()"
				  <?php if(isset($_POST['filterOptions'])){	echo ($_POST['filterOptions']=="income") ? 'checked':''; } ?>>
				  <label class="form-check-label" for="filter1">Income Status</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="filterOptions" id="filter2" value="medical" onclick="submit()"
				  <?php if(isset($_POST['filterOptions'])){	echo ($_POST['filterOptions']=="medical") ? 'checked':''; } ?>>
				  <label class="form-check-label" for="filter2">Medical Status</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="filterOptions" id="filter3" value="all" onclick="submit()"
				  <?php if(isset($_POST['filterOptions'])){	echo ($_POST['filterOptions']=="all") ? 'checked':''; } ?>>
				  <label class="form-check-label" for="filter3">Distance</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="filterOptions" id="filter4" value="eligibility" onclick="submit()"
				  <?php if(isset($_POST['filterOptions'])){	echo ($_POST['filterOptions']=="eligibility") ? 'checked':''; } ?>>
				  <label class="form-check-label" for="filter4">Eligibility</label>
				</div>
			</div>
			
		<?php	
				if($_POST['course']=="SHS"){
					$_POST['course'] = "Bachelor of Science Honours in Speech and Language Therapy";
				}
				else if($_POST['course']=="OT"){
					$_POST['course'] = "Bachelor of Science Honours in Occupational Therapy";
				}
				
				$hostel = "SELECT r.stureg_id, r.studentno, r.distance, (r.m_totincome + r.f_totincome + r.g_totincome) AS totincome, r.medical, r.med_cat, r.siblings, r.m_paysheet_tmp, r.f_paysheet_tmp, r.income_certificate_tmp, r.eligibility FROM registration r 
				WHERE r.applying_acayr = '".$_POST['acayr']."' AND r.batch = '".$_POST['batch']."' AND r.course = '".$_POST['course']."' AND r.gender = '".$_POST['gender']."' AND r.admit = '0' AND r.stureg_id = ( SELECT MAX(stureg_id) FROM registration WHERE studentno = r.studentno ) ";





				//echo $hostel;
				if(isset($_POST['filterOptions'])) {
					
					switch($_POST['filterOptions']) {
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
				}else{
				
					$hostel .= " ORDER BY studentno";
				}
				
				$hostel_sql = mysqli_query($conn, $hostel);
				// echo $hostel;
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
					<th>Distance</th>
					<th>Income (Rs.)</th>
					<th>Medical</th>
					<th>Siblings</th>
					<th>View Files</th>
					<!--<th>View Profile</th>-->
					<th>Eligibility</th>
				</tr>
				<?php
					
						
						$i=0;	
							
						while ( $hostel_raw=mysqli_fetch_assoc($hostel_sql)) {
							$stureg_id = $hostel_raw['stureg_id'];
							$studentno = $hostel_raw['studentno'];
							$distance = $hostel_raw['distance'];
							$medical = $hostel_raw['medical'];
							$siblings = $hostel_raw['siblings'];
							$totincome = $hostel_raw['totincome'];
							$med_cat = $hostel_raw['med_cat'];
							$eligibility = $hostel_raw['eligibility'];
							$m_pay = $hostel_raw['m_paysheet_tmp'];
							$f_pay = $hostel_raw['f_paysheet_tmp'];
							$income_cert = $hostel_raw['income_certificate_tmp'];
							
							$medical = ($medical==1) ? "Yes, ".$med_cat : "-" ;
							$siblings = ($siblings==1) ? "Yes" : "-" ;
							$i++;
				?>
			
				<tr>
					
					<td><input type="text" name="<?php echo 'si'.$i ?>" value="<?php echo $stureg_id ?>" hidden><?php  echo $studentno;  ?></td>
					<td><?php  echo $distance;  ?> km</td>
					<td style="text-align:right;"><?php echo number_format($totincome, 2, '.', ','); ?></td>
					<td><?php  echo $medical;  ?></td>
					<td><?php  echo $siblings;  ?></td>
					<td>
						<?php
						if($m_pay != null){
							?>

							<a target="_blank" href="https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $m_pay ?>">Mother's paysheet</a><br>

							<?php
						}
						if($f_pay != null){
							?>

							<a target="_blank" href="https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $f_pay ?>">Father's paysheet</a><br>

							<?php
						}
						if($income_cert != null){
							?>

							<a target="_blank" href="https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $income_cert ?>">Garama Niladari Certificate</a>

							<?php
						}

?>
						

						
					</td>
					<!--<td><a target="_blank" href="viewprofile.php?stureg_id=<?php //echo $stureg_id ?>"><i class="fa fa-address-card btn " style="background:green;color:white;padding:6px;" ></i></a></td>-->
					<td><input type="checkbox" value="1" id="eligibility<?php echo $i?>" name="eligibility<?php echo $i?>" <?php echo ($eligibility==1) ? "checked" : "" ; ?>></td>
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
			
			<div class="form-group" style="text-align:center;"  >
				<button type="submit" class="btn" style="background:#2F4F4F;color:white;padding:6px;" id="save" name="save" >Save
				<i class="fa fa-floppy-o" style="color:white;padding:6px;"> </i></button>
				<button type="submit" class="btn" style="background:#2F4F4F;color:white;padding:6px;" id="publish" name="publish" >Publish List
				<i class="fa fa-cloud-upload" style="color:white;padding:6px;"> </i></button>
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
		if(isset($_POST['save'])){
			
			for($i=1;$i<=$rows;$i++){
				$el = "eligibility".$i;
				$eligibility = ($_POST[$el]==1)?'1':'0';
				$si = "si".$i;
				$stureg_id = $_POST[$si];
				
				$save_sql .="UPDATE registration SET eligibility='".$eligibility."' WHERE stureg_id='".$stureg_id."';";
				
				
			}
			
			$run_save = mysqli_multi_query($conn, $save_sql);
						if($run_save){
							echo"<script>alert('Your Hostel Student List has been saved successfully!')</script>";
							echo "<meta http-equiv='refresh' content='0'>";
						}
		}
		if(isset($_POST['publish'])){
				require 'mail/gmail_api.php';
			 ?>
			 <script>
            if (confirm('Have you finalized and saved the list before proceeding?')) {
         <?php
         		

				$hostel1 = "SELECT `email` FROM `registration` WHERE `eligibility` = '1' AND `applying_acayr` = '".$_POST['acayr']."' AND `course` = '".$_POST['course']."' AND `batch` = '".$_POST['batch']."' AND `gender` = '".$_POST['gender']."' ";
				//echo "hos1".$hostel1;
				$hostel_sql1 = mysqli_query($conn, $hostel1);
				$rows1 = mysqli_num_rows($hostel_sql1);
				if($rows1>0){
					while ( $hostel_raw1=mysqli_fetch_assoc($hostel_sql1)) {
						$email1 .= $hostel_raw1['email'].",";
					}
					//api_sendMail("hostelmed@kln.ac.lk" ,"piumem@kln.ac.lk","Hostel Alerts",$email1);
					api_sendMail($email1 ,"","Hostel Alerts","You are eligible for hostel accommodation. Kindly proceed with the payment of the hostel fee amounting to Rs. 1,100.00. Please make the payment to the Shroff and upload your receipt through the Hostel Management System (HMS).");

				}

				$hostel2 = "SELECT `email` FROM `registration` WHERE `eligibility` = '0' AND `applying_acayr` = '".$_POST['acayr']."' AND `course` = '".$_POST['course']."' AND `batch` = '".$_POST['batch']."' AND `gender` = '".$_POST['gender']."' ";
				
				$hostel_sql2 = mysqli_query($conn, $hostel2);
				$rows2 = mysqli_num_rows($hostel_sql2);
				if($rows2>0){
					while ( $hostel_raw2=mysqli_fetch_assoc($hostel_sql2)) {
								$email2 .= $hostel_raw2['email'].",";
					}
				
				    //api_sendMail("hostelmed@kln.ac.lk" ,"piumem@kln.ac.lk","Hostel Alerts",$email2);
				api_sendMail($email2 ,"","Hostel Alerts","Sorry, You are not eligible for hostel accommodation. ");
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
</html>
