<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}

// Initialize POST variables to avoid undefined index warnings
if (!isset($_POST['acayr']))     $_POST['acayr'] = '';
if (!isset($_POST['course']))    $_POST['course'] = '';
if (!isset($_POST['batch']))     $_POST['batch'] = '';
if (!isset($_POST['gender']))    $_POST['gender'] = '';
if (!isset($_POST['payment']))   $_POST['payment'] = '';
if (!isset($_POST['save']))      $_POST['save'] = '';

// Initialize variables
$rows = 0;
$save_sql = '';
?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php'; ?>
<div class="container">
    <h2 class="text-center"><br>Selected List</h2><br><br>

    <!--Form starts here-->
    <form id="hoslist" action="" method="post" class="main-form">
        <div class="form-row">

            <!-- Academic Year -->
            <div class="col-md-3">
                <label for="acayr">Academic Year:</label>
                <select class="form-control" id="acayr" name="acayr" onchange="this.form.submit()">
                    <option value="">--Select Academic Year--</option>
                    <?php
                    // Get distinct academic years from registration
                    $acayr_query = "SELECT DISTINCT applying_acayr FROM registration ORDER BY applying_acayr DESC";
                    $acayr_sql = mysqli_query($conn, $acayr_query);
                    while ($row = mysqli_fetch_assoc($acayr_sql)) {
                        $aacayr = $row['applying_acayr'];
                        $selected = ($_POST['acayr'] == $aacayr) ? 'selected' : '';
                        echo "<option value='$aacayr' $selected>$aacayr</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Course -->
            <div class="col-md-3">
                <label for="course">Course:</label>
                <select class="form-control" id="course" name="course" onchange="this.form.submit()">
                    <option value="">--Select Course--</option>
                    <?php
                    if (!empty($_POST['acayr'])) {
                        $course_query = "SELECT DISTINCT course FROM registration WHERE applying_acayr = '" . $_POST['acayr'] . "' ORDER BY course";
                        $course_sql = mysqli_query($conn, $course_query);
                        while ($row = mysqli_fetch_assoc($course_sql)) {
                            $acourse = $row['course'];
                            $selected = ($_POST['course'] == $acourse) ? 'selected' : '';
                            echo "<option value='$acourse' $selected>$acourse</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Batch -->
            <div class="col-md-2">
                <label for="batch">Batch:</label>
                <select class="form-control" id="batch" name="batch" onchange="this.form.submit()">
                    <option value="">--Select Batch--</option>
                    <?php
                    if (!empty($_POST['acayr']) && !empty($_POST['course'])) {
                        $batch_query = "SELECT DISTINCT batch FROM registration WHERE applying_acayr = '" . $_POST['acayr'] . "' AND course = '" . $_POST['course'] . "' ORDER BY batch";
                        $batch_sql = mysqli_query($conn, $batch_query);
                        while ($row = mysqli_fetch_assoc($batch_sql)) {
                            $abatch = $row['batch'];
                            $selected = ($_POST['batch'] == $abatch) ? 'selected' : '';
                            echo "<option value='$abatch' $selected>$abatch</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Gender -->
            <?php if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch'])): ?>
                <div class="form-group col-md-2">
                    <label for="gender">Gender:</label>
                    <select class="form-control" id="gender" name="gender" onchange="this.form.submit()">
                        <option value="">--Select Gender--</option>
                        <option value="m" <?php if ($_POST['gender'] == 'm') echo 'selected'; ?>>Male</option>
                        <option value="f" <?php if ($_POST['gender'] == 'f') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Payment Status -->
            <?php if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender'])): ?>
                <div class="form-group col-md-2">
                    <label for="payment">Payment Status:</label>
                    <select class="form-control" id="payment" name="payment" onchange="this.form.submit()">
                        <option value="all" <?php if ($_POST['payment'] == 'all') echo 'selected'; ?>>All</option>
                        <option value="1" <?php if ($_POST['payment'] == '1') echo 'selected'; ?>>Paid</option>
                        <option value="0" <?php if ($_POST['payment'] == '0') echo 'selected'; ?>>Unpaid</option>
                    </select>
                </div>
            <?php endif; ?>

        </div>

        <?php
        // ===== Build the query if all filters are set =====
        if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender']) && isset($_POST['payment'])) {

            // Handle course name conversion (if needed)
            $course_display = $_POST['course'];
            if ($course_display == "SHS") {
                $course_display = "Bachelor of Science Honours in Speech and Language Therapy";
            } elseif ($course_display == "OT") {
                $course_display = "Bachelor of Science Honours in Occupational Therapy";
            }

            // Build base query: only eligible students (eligibility = 1)
            $hostel = "SELECT stureg_id, studentno, admit, payslip_tmp, payment 
                       FROM `registration` 
                       WHERE applying_acayr = '" . $_POST['acayr'] . "' 
                       AND batch = '" . $_POST['batch'] . "' 
                       AND course = '" . $course_display . "' 
                       AND gender = '" . $_POST['gender'] . "' 
                       AND eligibility = '1'";

            // Payment filter
            if ($_POST['payment'] == "1") {
                $hostel .= " AND payment = '1'";
            } elseif ($_POST['payment'] == "0") {
                $hostel .= " AND (payment = '0' OR payment IS NULL)";
            }
            // if 'all' – no extra condition

            $hostel .= " ORDER BY studentno";

            $hostel_sql = mysqli_query($conn, $hostel);
            $rows = mysqli_num_rows($hostel_sql);
        }
        ?>

        <?php if (isset($rows) && $rows > 0): ?>
            <div class="form-group">
                <table class="table table-hover mt-2">
                    <thead>
                        <tr>
                            <th>Student No</th>
                            <th>Payment Status</th>
                            <th>Payment Slip</th>
                            <th>Admit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        while ($row = mysqli_fetch_assoc($hostel_sql)) {
                            $stureg_id = $row['stureg_id'];
                            $studentno = $row['studentno'];
                            $admit = $row['admit'];
                            $payslip = $row['payslip_tmp'];
                            $payment = $row['payment'];
                            $i++;
                            ?>
                            <tr>
                                <td>
                                    <input type="text" name="si<?php echo $i; ?>" value="<?php echo $stureg_id; ?>" hidden>
                                    <?php echo htmlspecialchars($studentno); ?>
                                </td>
                                <td>
                                    <?php if ($payment == '1'): ?>
                                        <span class="badge badge-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($payslip)): ?>
                                        <a target="_blank" href="https://hosmed.kln.ac.lk/mail/tmp_files/<?php echo $payslip; ?>">View Payslip</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" id="admit<?php echo $i; ?>" name="admit<?php echo $i; ?>" <?php echo ($admit == 1) ? 'checked' : ''; ?>>
                                </td>
                                <td>
									<!-- this is only place holder now not send reminder emails -->
                                    <button class="btn btn-success btn-sm" <?php echo ($payment == 1) ? 'disabled' : ''; ?>>
										Send Reminder Email <i class="fa fa-envelope" style="color:white;padding:6px;"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="form-group" style="text-align:center;">
                <button type="submit" class="btn" style="background:#2F4F4F;color:white;padding:6px;" id="save" name="save" value="1">
                    Save <i class="fa fa-floppy-o" style="color:white;padding:6px;"></i>
                </button>
            </div>
        <?php elseif (isset($rows)): ?>
            <div class="alert alert-info">No eligible students found for the selected filters.</div>
        <?php endif; ?>

    </form>
</div>
<!-- footer -->
<?php include 'footer.php'; ?>

<?php
// ===== SAVE LOGIC =====
if (isset($_POST['save']) && $_POST['save'] == '1') {
    if (isset($rows) && $rows > 0) {
        $save_sql = '';
        for ($i = 1; $i <= $rows; $i++) {
            $ad = "admit" . $i;
            $admit = isset($_POST[$ad]) && $_POST[$ad] == 1 ? '1' : '0';
            $si = "si" . $i;
            $stureg_id = isset($_POST[$si]) ? $_POST[$si] : 0;
            if ($stureg_id > 0) {
                $save_sql .= "UPDATE registration SET admit='$admit' WHERE stureg_id='$stureg_id';";
            }
        }
        if (!empty($save_sql)) {
            $run_save = mysqli_multi_query($conn, $save_sql);
            if ($run_save) {
                echo "<script>alert('Your Hostel Student List has been saved successfully!')</script>";
                echo "<meta http-equiv='refresh' content='0'>";
            } else {
                echo "<script>alert('Error saving: " . mysqli_error($conn) . "')</script>";
            }
        } else {
            echo "<script>alert('No records to save!')</script>";
        }
    } else {
        echo "<script>alert('No records to save!')</script>";
    }
}
?>
</body>
</html>