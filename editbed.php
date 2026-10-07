<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}

include 'header.php';

// Initialize variables
$stuno = $gender = "";
$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;

// If student ID is provided in the URL, fetch student details
if ($sid > 0) {
    $sql_stuno = "SELECT `studentno`, `gender` FROM `registration` WHERE stureg_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_stuno)) {
        mysqli_stmt_bind_param($stmt, "i", $sid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $stuno, $gender);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
<!doctype html>
<html lang="en">
<!-- header already included -->
<div class="container">
    <div style="margin-bottom: 30px;">
        <h2 class="text-center"><br>Edit Student Bed - <?php echo htmlspecialchars($stuno); ?></h2><br><br>
    </div>

    <!--Form starts here-->
    <form id="apply" method="post" class="main-form needs-validation" novalidate>
        <!-- Hidden field to preserve student ID -->
        <input type="hidden" name="sid" value="<?php echo $sid; ?>">

        <div class="form-row">
            <!-- Select Hostel -->
            <div class="form-group col-md-3">
                <label for="hos1">Select Your Hostel:</label>
                <select class="form-control" id="hos1" name="hos1" onchange="this.form.submit()" required>
                    <option value="">--Select Hostel--</option>
                    <?php
                    $hostel_query = "SELECT hos_id FROM hostel WHERE gender = ? ORDER BY hos_id";
                    if ($stmt = mysqli_prepare($conn, $hostel_query)) {
                        mysqli_stmt_bind_param($stmt, "s", $gender);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $hos_id);
                        while (mysqli_stmt_fetch($stmt)) {
                            $selected = (isset($_POST['hos1']) && $_POST['hos1'] == $hos_id) ? 'selected' : '';
                            echo "<option value=\"$hos_id\" $selected>$hos_id</option>";
                        }
                        mysqli_stmt_close($stmt);
                    }
                    ?>
                </select>
            </div>

            <?php if (isset($_POST['hos1']) && !empty($_POST['hos1'])): ?>
                <!-- Select Floor using hostel_floor -->
                <div class="form-group col-md-3">
                    <label for="floor1">Select Your Floor:</label>
                    <select class="form-control" id="floor1" name="floor1" onchange="this.form.submit()" required>
                        <option value="">--Select Floor--</option>
                        <?php
                        $floor_query = "SELECT floor FROM hostel_floor WHERE hos_id = ? ORDER BY floor";
                        if ($stmt = mysqli_prepare($conn, $floor_query)) {
                            mysqli_stmt_bind_param($stmt, "s", $_POST['hos1']);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $floor_id);
                            while (mysqli_stmt_fetch($stmt)) {
                                $floor_label = ($floor_id == 0) ? "Ground Floor" : "Floor $floor_id";
                                $selected = (isset($_POST['floor1']) && $_POST['floor1'] == $floor_id) ? 'selected' : '';
                                echo "<option value=\"$floor_id\" $selected>$floor_label</option>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if (isset($_POST['floor1']) && !empty($_POST['floor1'])): ?>
                <!-- Select Room -->
                <div class="form-group col-md-3">
                    <label for="room1">Select Your Room:</label>
                    <select class="form-control" id="room1" name="room1" onchange="this.form.submit()" required>
                        <option value="">--Select Room--</option>
                        <?php
                        $room_query = "SELECT DISTINCT room_no FROM hostel_bed 
                                       WHERE hos_id = ? AND floor_no = ? 
                                       ORDER BY room_no";
                        if ($stmt = mysqli_prepare($conn, $room_query)) {
                            mysqli_stmt_bind_param($stmt, "si", $_POST['hos1'], $_POST['floor1']);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $room_id);
                            while (mysqli_stmt_fetch($stmt)) {
                                // Pad room number to 2 digits if needed (for display)
                                $room_display = str_pad($room_id, 2, '0', STR_PAD_LEFT);
                                $selected = (isset($_POST['room1']) && $_POST['room1'] == $room_id) ? 'selected' : '';
                                echo "<option value=\"$room_id\" $selected>$room_display</option>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if (isset($_POST['room1']) && !empty($_POST['room1'])): ?>
                <!-- Select Bed -->
                <div class="form-group col-md-3">
                    <label for="bed1">Select Your Bed No.:</label>
                    <select class="form-control" id="bed1" name="bed1" required>
                        <option value="">--Select Bed--</option>
                        <?php
                        $bed_query = "SELECT bed_id, bed_no FROM hostel_bed 
                                      WHERE hos_id = ? AND floor_no = ? AND room_no = ? AND availability = 1 
                                      ORDER BY bed_no";
                        if ($stmt = mysqli_prepare($conn, $bed_query)) {
                            mysqli_stmt_bind_param($stmt, "sii", $_POST['hos1'], $_POST['floor1'], $_POST['room1']);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $bed_id, $bed_no);
                            while (mysqli_stmt_fetch($stmt)) {
                                $selected = (isset($_POST['bed1']) && $_POST['bed1'] == $bed_id) ? 'selected' : '';
                                echo "<option value=\"$bed_id\" $selected>$bed_no</option>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary" id="register" name="register">Save</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>

<?php
// ============================================================
// FORM SUBMISSION HANDLER
// ============================================================
if (isset($_POST['register'])) {
    $bed_id = $_POST['bed1'];
    $sid = $_POST['sid']; // from hidden field

    // Update registration: assign bed, set admit = 1
    $sql_reg = "UPDATE registration SET bed_id = ?, admit = '1' WHERE stureg_id = ?";
    $stmt_reg = mysqli_prepare($conn, $sql_reg);
    mysqli_stmt_bind_param($stmt_reg, "ii", $bed_id, $sid);
    $reg_ok = mysqli_stmt_execute($stmt_reg);
    mysqli_stmt_close($stmt_reg);

    // Update hostel_bed: mark as occupied
    $sql_bed = "UPDATE hostel_bed SET availability = '0' WHERE bed_id = ?";
    $stmt_bed = mysqli_prepare($conn, $sql_bed);
    mysqli_stmt_bind_param($stmt_bed, "i", $bed_id);
    $bed_ok = mysqli_stmt_execute($stmt_bed);
    mysqli_stmt_close($stmt_bed);

    if ($reg_ok && $bed_ok) {
        echo "<script>alert('Your record has been successfully updated!')</script>";
        echo "<script>window.location = 'viewcurrent.php';</script>";
    } else {
        echo "<script>alert('Error updating record. Please try again.')</script>";
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