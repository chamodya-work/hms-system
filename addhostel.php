<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: account/login.php");
    exit;
}

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
<!doctype html>
<html lang="en">
  <!-- header-->
	<?php include 'header.php'; ?>
	
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h2 class="text-center mt-4 mb-4">Add Hostel Details</h2>
			</div>
		</div>
		
		<!--Form starts here-->
		<form id="addhostel" action="" method="post" class="needs-validation" novalidate>
			
			<!-- Initial Input Row -->
			<div class="row justify-content-center mb-4">
				
				<!-- Hostel ID -->	
				<div class="col-md-3 col-lg-2">
					<label for="hos_id">Hostel ID:</label>
					<input type="text" class="form-control" 
                        name="hos_id" 
                        pattern="[A-Za-z][0-9]+" 
                        placeholder="e.g., A123"
                        value="<?php echo isset($_POST['hos_id']) ? htmlspecialchars($_POST['hos_id']) : ''; ?>" 
                        required>
					<div class="invalid-feedback">
						Please provide a valid hostel ID (letter followed by numbers).
					</div>
				</div>
				
				<!-- Gender -->	
				<div class="col-md-3 col-lg-2">
					<label for="gender">Gender:</label>
					<select class="form-control" id="gender" name="gender" required>
						<option value="">Select...</option>
						<option value="M" <?php if(isset($_POST['go'])){
												echo ($_POST['gender']=="M") ? 'selected':''; } ?>>Male</option>
						<option value="F" <?php if(isset($_POST['go'])){
												echo ($_POST['gender']=="F") ? 'selected':''; } ?>>Female</option>	
					</select>
					<div class="invalid-feedback">
						Please select a gender.
					</div>
				</div>
				
				<!-- Floors -->	
				<div class="col-md-3 col-lg-2">
					<label for="tot_floors">No. of Floors:</label>
					<input type="number" 
                        min="1" 
                        max="5" 
                        class="form-control" 
                        <?php if(isset($_POST['go'])){ echo "value='".$_POST['tot_floors']."'" ;}?> 
                        name="tot_floors" 
                        required>
					<div class="invalid-feedback">
						Please enter 1-5 floors.
					</div>
				</div>
				
				<!-- Go Button -->
				<div class="col-md-3 col-lg-2">
					<label>&nbsp;</label>
					<button type="submit" class="btn btn-primary btn-block" id="go" name="go">Go</button>
				</div>
				
			</div>
			
			<?php
				// Display floor-room-bed table after Go is clicked
				if((isset($_POST['go'])) AND ($_POST['tot_floors']>0)){
					$tot_floors = $_POST['tot_floors'];
			?>		
			
			<!-- Floor-Room-Bed Table Section -->
			<div class="row justify-content-center">
				<div class="col-lg-8 col-md-10">
					
					<h5 class="mb-3">Configure Each Floor:</h5>
					
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<thead class="thead-dark">
								<tr>
									<th scope="col" class="text-center">Floor No.</th>
									<th scope="col" class="text-center">Rooms per Floor</th>
									<th scope="col" class="text-center">Beds per Room</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								$i=0;
								while($i<$tot_floors){
							?>
								<tr>
									<td class="text-center align-middle">
										<input type="text" 
                                            class="form-control text-center" 
                                            value="<?php echo $i ?>" 
                                            name="hos_floor<?php echo $i ?>" 
                                            readonly>
									</td>
									<td>
										<input type="number" 
                                            min="1" 
                                            max="100" 
                                            class="form-control" 
                                            name="hos_room<?php echo $i ?>" 
                                            placeholder="1-100"
                                            required>
									</td>
									<td>
										<input type="number" 
                                            min="1" 
                                            max="20" 
                                            class="form-control" 
                                            name="hos_bed<?php echo $i ?>" 
                                            placeholder="1-20"
                                            required>
									</td>
								</tr>
							<?php
								$i++;
								}
							?>
							</tbody>
						</table>
					</div>
					
					<!-- Final Submit Button -->
					<div class="text-center mt-4 mb-5">
						<button type="submit" class="btn btn-success btn-lg px-5" id="addhos" name="addhos">
							<i class="fas fa-plus-circle"></i> Add Hostel Record
						</button>
					</div>
					
				</div>
			</div>
			
			<?php
				}
			?>
			
		</form> 
		
	</div>
	
	<!-- footer -->
	<?php include 'footer.php'; ?>
	
	<?php
    if (isset($_POST['addhos'])) {
        $hos_id     = sanitize_input($_POST['hos_id']);
        $gender     = sanitize_input($_POST['gender']);
        $tot_floors = (int)$_POST['tot_floors'];

        // Validation
        if (!preg_match('/^[A-Za-z][0-9]+$/', $hos_id)) die("Invalid Hostel ID format.");
        if (!in_array($gender, ['M', 'F'])) die("Invalid gender.");
        if ($tot_floors < 1 || $tot_floors > 5) die("Invalid number of floors.");

        // Insert into hostel
        $stmt1 = $conn->prepare("INSERT INTO hostel (`hos_id`, `hos_floors`, `gender`) VALUES (?, ?, ?)");
        $stmt1->bind_param("sis", $hos_id, $tot_floors, $gender);
        $stmt1->execute();

        // Floor and bed inserts
        $stmt2 = $conn->prepare("INSERT INTO hostel_floor (`hos_id`, `floor`, `rooms`, `beds`) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("siii", $hos_id, $floor, $rooms, $beds);

        $stmt3 = $conn->prepare("INSERT INTO hostel_bed (`hos_id`, `floor_no`, `room_no`, `bed_no`, `availability`) VALUES (?, ?, ?, ?, 1)");
        $stmt3->bind_param("siii", $hos_id, $floor_no, $room_no, $bed_no);

        for ($i = 0; $i < $tot_floors; $i++) {
            $floor = (int)$_POST["hos_floor$i"];
            $rooms = (int)$_POST["hos_room$i"];
            $beds  = (int)$_POST["hos_bed$i"];

            $floor_no = $floor;
            $stmt2->execute();

            for ($r = 1; $r <= $rooms; $r++) {
                $room_no = $r;
                for ($b = 1; $b <= $beds; $b++) {
                    $bed_no = $b;
                    $stmt3->execute();
                }
            }
        }

        echo "<script>alert('Hostel details have been successfully added!');</script>";
        echo "<script>window.location='hostellist.php';</script>";
        exit;
    }
    ?>
	
	<!-- JavaScript: Bootstrap validation -->   
    <script>
		var form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
	</script>
 </body>
</html>