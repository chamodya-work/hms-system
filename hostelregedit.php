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
	<?php include 'header.php'; 
	
	$hosreg_id = $_GET['hosreg_id'];
	
	//get registraion details
	$hosreg = "SELECT * FROM `hostel_reg` where hosreg_id = $hosreg_id ;";
	$hosreg_sql = mysqli_query($conn, $hosreg);
	$hosreg_raw=mysqli_fetch_assoc($hosreg_sql);
	$rego = $hosreg_raw['rego'];
	$regc = $hosreg_raw['regc'];
	$acayr = $hosreg_raw['acayr'];
	$batch = $hosreg_raw['batch'];
	$course = $hosreg_raw['course'];
	?>
	<div class="container" >
		<h2 class="text-center"><br>Hostel Registration</h2><br><br>
		
		<!--Form starts here-->
		<form id="addhostel" action=""  method="post" class="main-form needs-validation"  novalidate>
			
			
			<div class="form-row">
				
				<!-- reg open -->	
				<div class="form-group col-md-4">
					<label for="rego">Registration Open:</label>
					<input type="date" class="form-control"   name="rego" value="<?php echo $rego  ?>" required >
				</div>
				<!-- reg close -->	
				<div class="form-group col-md-4">
					<label for="regc">Registration Close:</label>
					<input type="date" class="form-control" name="regc" value="<?php echo $regc  ?>"   required>
				</div>
				
				
			</div>
			<div class="form-row">
				<!-- aca year -->	
				<div class="form-group col-md-4">
					<label for="acayr">Academic Year:</label>
					<input type="text" class="form-control" name="acayr" value="<?php echo $acayr  ?>"    pattern="[0-9]{4}/[0-9]{4}" >
				</div>
				<!-- batch -->	
				<div class="form-group col-md-4">
					<label for="batch">Batch:</label>
					<input type="number" class="form-control" name="batch" value="<?php echo $batch  ?>"  >
				</div>
				<!-- Course -->	
				<div class="form-group col-md-4">
					<label for="course">Course:</label>
					<select class="form-control" id="course" name="course" value="<?php echo $course  ?>"  required>
						<option value="Medicine">MBBS</option>
						<option value="SHS">SHS</option>	
						<option value="OT">OT</option>	
					</select>
				</div>
				<div class="form-group col-md-2"></div>
				
				
			</div>
			
			
			
			
			<div class="form-group" style="text-align:center;" >
				<button type="submit" class="btn btn-primary" id="reghos" name="reghos" >Update</button>
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
			
			$add_sql1 = "UPDATE `hostel_reg` SET `rego`='".$rego."',`regc`='".$regc."',`acayr`='".$acayr."',`batch`='".$batch."',`course`='".$course."' WHERE `hosreg_id`='".$hosreg_id."'; ";
			$run_add1 = mysqli_query($conn, $add_sql1);
			
			if($run_add1){
				echo"<script>alert('Hostel details has been successfully updated!')</script>";
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
	</script>
 </body>
</html>
