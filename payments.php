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
	
	
	?>
	<div class="container" >
		
		<?php
			include 'getData.php';
			if (isset($_SESSION['student_data'])) {
				$data = $_SESSION['student_data'];
				//get student information from central DB
				
				$stnm = $data['data']['StudentNumber'];
				$fname = $data['data']['fullName'];
				
				$acayr_sql = "SELECT `stureg_id`, `applying_acayr` FROM `registration` WHERE `studentno` = ? ORDER BY stureg_id DESC LIMIT 1;";
				$stmt = $conn->prepare($acayr_sql);
				$stmt->bind_param("s", $stnm); 
				
				if ($stmt->execute()) {
				    
				    $result = $stmt->get_result();
				    if ($result->num_rows > 0) {
				        $row = $result->fetch_assoc();
				        $stureg_id = $row['stureg_id'];
				        $acayr = $row['applying_acayr'];
				        //echo $stureg_id; 
				    } 
				} else {
				    echo "Query failed: " . $stmt->error;
				}

				$stmt->close();
			}
		?>

		<div style="margin-bottom: 30px;">
		<h2 class="text-center"><br>Hostel Payments - <?php  echo $acayr ;  ?> 
		
		</h2>  <br><br>
		</div>
		
		
		<!--Form starts here-->
		<form id="apply" action=""  method="post" class="main-form needs-validation" enctype="multipart/form-data"  novalidate>
			<div class="form-row">
			
				<!-- Student Number -->	
				<div class="form-group col-md-3">
					<label for="stnm">Student Number:</label>
					<input type="text" class="form-control" value="<?php echo  $stnm ?>"  name="stnm"  readonly>
				</div>
				<!-- full name -->	
				<div class="form-group col-md-6">
					<label for="fname">Full Name:</label>
					<input type="text" class="form-control" value="<?php echo  $fname ?>"  name="fname"  readonly>
				</div>
				
			</div>
			
			<div class="form-row" style="margin-bottom:30px;">
                <div class="input-group col-md-6">
                <input type="file" class="form-control" id="payslip"  name ="payslip" aria-describedby="upload" aria-label="Upload" accept="application/pdf">
                <button class="btn btn-primary" type="submit" id="upload" name="upload">Submit</button>
            </div> 
			
			
			
		</form> 
	<!--footer-->	
	</div>
	<!-- footer -->
	<?php include 'footer.php'; ?>
	
	
		<?php
		//form submission code
		if(isset($_POST['upload'])){
				
                if (isset($_FILES['payslip']) && $_FILES['payslip']['error'] === UPLOAD_ERR_OK) {
					$fileName = $_FILES['payslip']['tmp_name'];
					$originalFileName = $_FILES['payslip']['name'];
					$fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
					$uniqueFileName = uniqid('pay_', true) . '.' . $fileExtension;
					$destination = 'mail/tmp_files/' . $uniqueFileName;
					if(move_uploaded_file($fileName, $destination)){
						//$m_paysheet = uploadFile($uniqueFileName,'13Laqaz1JOoMbP2877N9F_qpiNpfgMsCo');
						$payslip_tmp = $uniqueFileName;

						$register_sql = "UPDATE `registration` SET `payslip_tmp`='$payslip_tmp', `payment`='1' WHERE `stureg_id` = '$stureg_id'  ";
				
						//echo $register_sql;
						$run_register = mysqli_query($conn, $register_sql);
					}						
				}
				
				

				if($run_register){
					require 'mail/gmail_api.php';
					api_sendMail($email ,"piumem@kln.ac.lk","Hostel Alerts","Your payment receipt has been successfully submitted!");
					//api_sendMail("madushanijap@gmail.com","hostelmed@kln.ac.lk","Hostel Alerts","Your payment receipt has been successfully submitted!");
					echo"<script>alert('Your payment receipt has been successfully submitted!')</script>";
					echo "<meta http-equiv='refresh' content='0'>";
					echo"<script> window.location =  'index.php' ; </script>     ";
					
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
