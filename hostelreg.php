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
		<h2 class="text-center page-title"><i class="fa fa-paper-plane"></i> Open Hostel Registration</h2>
		
		<!--Form starts here-->
		<form id="addhostel" action=""  method="post" class="main-form needs-validation"  novalidate>
			
			
			<div class="form-row">
				<!-- aca year -->
				 <?php
					// Get current academic year in format YYYY/YYYY
					$aca_sql = "SELECT id,academic_year FROM academic_year WHERE is_current='1'";
					$aca_run = mysqli_query($conn, $aca_sql);
					$aca_row = mysqli_fetch_assoc($aca_run);
					$current_acayr = $aca_row['academic_year'];
					$current_acayr_id = $aca_row['id'];

				?>	
				<div class="form-group col-md-4">
					<label for="acayr">Academic Year:</label>
					<input type="text" class="form-control"  value=<?php echo $current_acayr; ?>  readonly>
					<input type="hidden" name="acayr" value="<?php echo $current_acayr_id; ?>">
				</div>
				
				<!-- Course -->	
				<div class="form-group col-md-4">
					<label for="course">Course:</label>
					<select class="form-control" id="course" name="course" required>
						<option value="Medicine">MBBS</option>
						<option value="SHS">SHS</option>	
						<option value="OT">OT</option>	
					</select>
				</div>
				
				<!-- batch -->	
				<div class="form-group col-md-4">
					<label for="batch">Batch:</label>
					<input type="number" min='1' max='100' class="form-control" name="batch"  required>
				</div>
				

				
				
				
			</div>
			<div class="form-row">
				
				<!-- reg open -->	
				<div class="form-group col-md-4">
					<label for="rego">Registration Open:</label>
					<input type="date" class="form-control"   name="rego" required >
				</div>
				<!-- reg close -->	
				<div class="form-group col-md-4">
					<label for="regc">Registration Close:</label>
					<input type="date" class="form-control" name="regc"  required>
				</div>
				
				
			</div>
			
			
			<div class="form-group" style="text-align:center;" >
				<button type="submit" class="btn-lg btn-block open-reg-btn" id="reghos" name="reghos" >Add Record</button>
			</div>
		
			
			
		</form> 
	<!--footer-->	
	</div>
	<!-- footer -->
	<?php include 'footer.php'; ?>
	
	
		<?php
		//form submission code
		if(isset($_POST['reghos'])){
			
			$rego = $_POST['rego'];
			$regc = $_POST['regc'];
			$acayr = $_POST['acayr'];
			$batch = $_POST['batch'];
			$course = $_POST['course'];
			
			$add_sql1 = "INSERT INTO `hostel_reg`( `rego`, `regc`, `acayr`, `batch`, `course`) VALUES ('".$rego."','".$regc."','".$acayr."','".$batch."','".$course."');";
			$run_add1 = mysqli_query($conn, $add_sql1);
			if($run_add1){
					echo"<script>alert('Hostel details has been successfully added!')</script>";
					echo "<script>window.location='hosreglist.php';</script>";
				}
		} 

		
		?>
	<!-- JavaScript -->    
    <script>
		var form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
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
