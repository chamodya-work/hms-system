<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php'; ?>
<div class="container">
    <div style="margin-bottom: 30px;">
        <h2 class="text-center"><br>Request for Maintenance & Repair</h2> <br><br>
    </div>

    <?php
    // ====================================================
    // STUDENT MODE (cat = 1)
    // ====================================================
    if ($_SESSION["cat"] == '1') {

        include 'getData.php';
        if (isset($_SESSION['student_data'])) {
            $data = $_SESSION['student_data'];
            $stnm = $data['data']['StudentNumber'];
        }

        // Fetch student's current bed allocation
        $hos = "SELECT hb.hos_id, hb.floor_no, hb.room_no, r.bed_id
                FROM registration r
                JOIN hostel_bed hb ON hb.bed_id = r.bed_id
                WHERE r.studentno = '$stnm'
                ORDER BY r.stureg_id DESC LIMIT 1";
        $hos_sql = mysqli_query($conn, $hos);
        $hos_raw = mysqli_fetch_assoc($hos_sql);
        if ($hos_raw) {
            $hos_id   = $hos_raw['hos_id'];
            $floor_id = $hos_raw['floor_no'];
            $room_id  = $hos_raw['room_no'];
            $bed_id   = $hos_raw['bed_id'];
            ?>

            <!-- STUDENT REPAIR REQUEST FORM -->
            <form id="apply" action="" method="post" class="main-form needs-validation" novalidate enctype="multipart/form-data">
                <div class="form" style="width: 50%; margin: auto;">

                    <!-- Student Number (readonly) -->
                    <div class="form-group">
                        <label for="stnm">Student Number:</label>
                        <input type="text" class="form-control" value="<?php echo $stnm; ?>" name="stnm" readonly>
                    </div>

                    <!-- Hostel (readonly) -->
                    <div class="form-group">
                        <label>Hostel:</label>
                        <input type="text" class="form-control" value="<?php echo $hos_id; ?>" name="hos" readonly>
                    </div>

                    <!-- Floor (readonly) -->
                    <div class="form-group">
                        <label>Floor:</label>
                        <input type="text" class="form-control" value="<?php echo $floor_id; ?>" name="floor" readonly>
                    </div>

                    <!-- Room (readonly) -->
                    <div class="form-group">
                        <label>Room:</label>
                        <input type="text" class="form-control" value="<?php echo $room_id; ?>" name="room" readonly>
                    </div>

                    <!-- CHANGED: Contact Number 1 – now mandatory -->
                    <div class="form-group">
                        <label for="contact">Contact Number 1 (required):</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>

                    <!-- NEW: Contact Number 2 – optional -->
                    <div class="form-group">
                        <label for="contact2">Contact Number 2 (optional):</label>
                        <input type="text" class="form-control" name="contact2">
                    </div>

                    <!-- CHANGED: Category dropdown – added "Wi-Fi Issues" -->
                    <div class="form-group">
                        <label for="cat">Category of the request:</label>
                        <select class="form-control" id="cat" name="cat" required>
                            <option value="">--Select Category--</option>
                            <option value="Civil">Civil</option>
                            <option value="Electrical">Electrical</option>
                            <option value="Health">Health</option>
                            <option value="Landscape">Landscape</option>
                            <option value="Cleaning">Cleaning</option>
                            <!-- NEW Wi-Fi category -->
                            <option value="Wi-Fi Issues">Wi-Fi Issues</option>
                        </select>
                    </div>

                    <!-- CHANGED: Removed Nature dropdown and Remarks textarea.
                         Replaced with a single "Details of the request" text field. -->
                    <div class="form-group">
                        <label for="description">Details of the request:</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>

                    <!-- Image upload (optional) -->
                    <div class="form-group">
                        <label for="file">Upload an image (optional):</label>
                        <input type="file" class="form-control-file" id="file" name="file" accept="image/*">
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary" id="register" name="register">Send Request</button>
                    </div>
                </div>
            </form>

            <?php
        } else {
            echo "<div class='alert alert-warning'>You are not currently allocated a bed. Please contact the hostel office.</div>";
        }
    }

    // ====================================================
    // STAFF MODE (cat = 2)
    // ====================================================
    if ($_SESSION["cat"] == '2') {
        if (isset($_GET['rid'])) {
            $rid = $_GET['rid'];
            // Fetch repair details along with bed info
            $select = "SELECT r.* , hb.* FROM `repairs` r LEFT JOIN hostel_bed hb ON r.bed_id = hb.bed_id WHERE `rep_id` = '$rid'";
            $run_select = mysqli_query($conn, $select);
            if ($run_select && mysqli_num_rows($run_select) > 0) {
                $repair_raw = mysqli_fetch_assoc($run_select);
                $req_date   = $repair_raw['req_date'];
                $stu_no     = $repair_raw['stu_no'];
                $contact    = $repair_raw['contact'];
                $contact2   = $repair_raw['contact2'] ?? ''; // new field
                $cat        = $repair_raw['cat'];
                $desc       = $repair_raw['description'];   // details of request
                $hos_id     = $repair_raw['hos_id'];
                $floor_no   = $repair_raw['floor_no'];
                $room_no    = $repair_raw['room_no'];
                $status     = $repair_raw['status'];
                // nature column is no longer used; we ignore it.

                if ($status != "Pending") {
                    $infodate = $repair_raw['info_date'];
                    $infoto   = $repair_raw['info_to'];
                }
                if ($status == "Completed") {
                    $dtcomplete = $repair_raw['completed_date'];
                    $obs        = $repair_raw['observer'];
                    $workers    = $repair_raw['attended_workers'];
                }
                ?>

                <!-- STAFF VIEW / UPDATE FORM -->
                <form id="apply" action="" method="post" class="main-form needs-validation" novalidate>
                    <!-- FIX: Hidden rid to preserve it on POST -->
                    <input type="hidden" name="rid" value="<?php echo $rid; ?>">

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Student Number:</label>
                            <input type="text" class="form-control" value="<?php echo $stu_no; ?>" readonly>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Hostel:</label>
                            <input type="text" class="form-control" value="<?php echo $hos_id; ?>" readonly>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Floor:</label>
                            <input type="text" class="form-control" value="<?php echo $floor_no; ?>" readonly>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Room:</label>
                            <input type="text" class="form-control" value="<?php echo $room_no; ?>" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Contact Number 1:</label>
                            <input type="text" class="form-control" value="<?php echo $contact; ?>" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Contact Number 2:</label>
                            <input type="text" class="form-control" value="<?php echo $contact2; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Category:</label>
                            <input type="text" class="form-control" value="<?php echo $cat; ?>" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Request Date:</label>
                            <input type="text" class="form-control" value="<?php echo $req_date; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Details of the request:</label>
                            <textarea class="form-control" readonly><?php echo $desc; ?></textarea>
                        </div>
                    </div>

                    <?php
                    // Show status-specific fields
                    if ($status != "Completed") {
                        ?>
                        <hr>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Date Informed:</label>
                                <input type="date" class="form-control" name="dt" <?php if ($status != 'Pending') echo 'value="' . $infodate . '" readonly'; ?>>
                            </div>
                            <div class="form-group col-md-8">
                                <label>Informed To:</label>
                                <input type="text" class="form-control" name="infoto" <?php if ($status != 'Pending') echo 'value="' . $infoto . '" readonly'; ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="addeval" name="addeval" <?php if ($status != 'Pending') echo 'hidden'; ?>>Save</button>
                        </div>
                        <?php
                    }

                    if ($status == "Informed") {
                        ?>
                        <hr>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Date Completed:</label>
                                <input type="date" class="form-control" name="dtc" <?php if ($status == 'Completed') echo 'value="' . $dtcomplete . '" readonly'; ?>>
                            </div>
                            <div class="form-group col-md-8">
                                <label>Observed By:</label>
                                <input type="text" class="form-control" name="obs" <?php if ($status == 'Completed') echo 'value="' . $obs . '" readonly'; ?>>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Attended Workers:</label>
                                <textarea style="width: 100%; height: 100px;" name="workers" <?php if ($status == 'Completed') echo 'readonly'; ?>><?php if ($status == 'Completed') echo $workers; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="addobs" name="addobs" <?php if ($status == 'Completed') echo 'hidden'; ?>>Save</button>
                        </div>
                        <?php
                    }
                    ?>
                </form>

                <?php
            } else {
                echo "<div class='alert alert-danger'>Repair request not found.</div>";
            }
        } else {
            echo "<div class='alert alert-info'>Please select a repair request to view.</div>";
        }
    }
    ?>
</div>

<!-- footer -->
<?php include 'footer.php'; ?>

<?php
// ====================================================
// STUDENT SUBMISSION (POST register)
// ====================================================
if (isset($_POST['register'])) {
    $contact  = $_POST['contact'];
    $contact2 = isset($_POST['contact2']) ? $_POST['contact2'] : '';
    $cat      = $_POST['cat'];
    $desc     = $_POST['description'];  // details of request
    $dest_path = NULL;

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName    = $_FILES['file']['name'];
        $uploadFileDir = './images/';
        $dest_path = $uploadFileDir . $fileName;
        if (!move_uploaded_file($fileTmpPath, $dest_path)) {
            $dest_path = NULL; // reset on failure
        }
    }

    // Insert query – now includes contact2 and uses description for details.
    // Nature column is set to empty string (we no longer use it).
    $register_sql = "INSERT INTO `repairs` 
        (`req_date`, `stu_no`, `cat`, `contact`, `contact2`, `nature`, `description`, `status`, `bed_id`, `file_path`)
        VALUES 
        (CURDATE(), '$stnm', '$cat', '$contact', '$contact2', '', '$desc', 'Pending', '$bed_id', '$dest_path')";

    $run_register = mysqli_query($conn, $register_sql);

    if ($run_register) {
        // (Optional) uncomment email sending if needed
        // require 'mail/gmail_api.php';
        // api_sendMail($email, "piumem@kln.ac.lk", "Hostel Alerts", "New repair request submitted.");
        echo "<script>alert('Your request has been successfully submitted!')</script>";
        echo "<script> window.location =  'index.php' ; </script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
    }
}

// ====================================================
// STAFF – UPDATE STATUS TO "INFORMED" (POST addeval)
// ====================================================
if (isset($_POST['addeval'])) {
    $rid = $_POST['rid'];  // Now we have it from hidden input
    $infodate = $_POST['dt'];
    $infoto   = $_POST['infoto'];

    $update_sql = "UPDATE `repairs` SET `info_date`='$infodate', `info_to`='$infoto', `status`='Informed' WHERE `rep_id`='$rid'";
    $run_update = mysqli_query($conn, $update_sql);

    if ($run_update) {
        // (Optional) send email notification
        echo "<script>alert('Repair status has been successfully updated to Informed!')</script>";
        echo "<script> window.location =  'index.php' ; </script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
    }
}

// ====================================================
// STAFF – UPDATE STATUS TO "COMPLETED" (POST addobs)
// ====================================================
if (isset($_POST['addobs'])) {
    $rid = $_POST['rid'];  // Now we have it from hidden input
    $dtcomplete = $_POST['dtc'];
    $obs        = $_POST['obs'];
    $workers    = $_POST['workers'];

    $update_sql = "UPDATE `repairs` SET 
        `completed_date`='$dtcomplete', 
        `observer`='$obs', 
        `attended_workers`='$workers', 
        `status`='Completed' 
        WHERE `rep_id`='$rid'";
    $run_update = mysqli_query($conn, $update_sql);

    if ($run_update) {
        // (Optional) send email notification
        echo "<script>alert('Repair status has been successfully updated to Completed!')</script>";
        echo "<script> window.location =  'index.php' ; </script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
    }
}
?>

<script>
    var form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function (event) {
        if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    })
</script>
</body>
</html>