<?php
// Initialize the session
session_start();

// Initialize POST variables to avoid undefined index warnings
if (!isset($_POST['acayr'])) $_POST['acayr'] = '';
if (!isset($_POST['course'])) $_POST['course'] = '';
if (!isset($_POST['batch'])) $_POST['batch'] = '';
if (!isset($_POST['gender'])) $_POST['gender'] = '';
if (!isset($_POST['filterOptions'])) $_POST['filterOptions'] = '';
// Do NOT initialize 'save' or 'publish' – they should only exist when submitted

// Initialize variables to avoid undefined warnings
$rows = 0;
$save_sql = '';

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
    <h2 class="text-center"><br>Hostel Applications List</h2><br><br>

    <!-- ===== FILTER FORM ===== -->
    <form id="filterForm" action="" method="post">
        <div class="form-row">
            <!-- Academic Year -->
            <div class="form-group col-md-3">
                <label for="acayr">Academic Year:</label>
                <select class="form-control" id="acayr" name="acayr" onchange="this.form.submit()">
                    <option value="">--Select Academic Year--</option>
                    <?php
                    $acayr = "SELECT acayr FROM hostel_reg WHERE acayr!='0' GROUP BY acayr ORDER BY acayr DESC";
                    $acayr_sql = mysqli_query($conn, $acayr);
                    while ($acayr_raw = mysqli_fetch_assoc($acayr_sql)) {
                        $aacayr = $acayr_raw['acayr'];
                    ?>
                        <option value="<?php echo $aacayr; ?>" <?php if ($_POST['acayr'] == $aacayr) echo 'selected'; ?>>
                            <?php echo $aacayr; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <!-- Course -->
            <?php if (!empty($_POST['acayr'])): ?>
                <div class="form-group col-md-3">
                    <label for="course">Course:</label>
                    <select class="form-control" id="course" name="course" onchange="this.form.submit()">
                        <option value="">--Select Course--</option>
                        <?php
                        $course = "SELECT DISTINCT course FROM registration WHERE applying_acayr = '" . $_POST['acayr'] . "' ORDER BY course";
                        $course_sql = mysqli_query($conn, $course);
                        while ($course_raw = mysqli_fetch_assoc($course_sql)) {
                            $acourse = $course_raw['course'];
                        ?>
                            <option value="<?php echo $acourse; ?>" <?php if ($_POST['course'] == $acourse) echo 'selected'; ?>>
                                <?php echo $acourse; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Batch -->
            <?php if (!empty($_POST['acayr']) && !empty($_POST['course'])): ?>
                <div class="form-group col-md-3">
                    <label for="batch">Batch:</label>
                    <select class="form-control" id="batch" name="batch" onchange="this.form.submit()">
                        <option value="">--Select Batch--</option>
                        <?php
                        $batch = "SELECT DISTINCT batch FROM registration WHERE applying_acayr = '" . $_POST['acayr'] . "' AND course='" . $_POST['course'] . "' ORDER BY batch";
                        $batch_sql = mysqli_query($conn, $batch);
                        while ($batch_raw = mysqli_fetch_assoc($batch_sql)) {
                            $abatch = $batch_raw['batch'];
                        ?>
                            <option value="<?php echo $abatch; ?>" <?php if ($_POST['batch'] == $abatch) echo 'selected'; ?>>
                                <?php echo $abatch; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Gender -->
            <?php if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch'])): ?>
                <div class="form-group col-md-3">
                    <label for="gender">Gender:</label>
                    <select class="form-control" id="gender" name="gender" onchange="this.form.submit()">
                        <option value="">--Select Gender--</option>
                        <option value="m" <?php if ($_POST['gender'] == 'm') echo 'selected'; ?>>Male</option>
                        <option value="f" <?php if ($_POST['gender'] == 'f') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    </form>

    <!-- ===== SORT RADIO BUTTONS (still part of filter, but inside same filter form?) 
          We'll put them inside filter form as well, and submit on click -->
    <?php if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender'])): ?>
        <form id="filterForm2" action="" method="post">
            <input type="hidden" name="acayr" value="<?php echo $_POST['acayr']; ?>">
            <input type="hidden" name="course" value="<?php echo $_POST['course']; ?>">
            <input type="hidden" name="batch" value="<?php echo $_POST['batch']; ?>">
            <input type="hidden" name="gender" value="<?php echo $_POST['gender']; ?>">
            <div class="form-row" style="margin-bottom:20px">
                <div class="form-check-inline">Sort list by:</div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterOptions" value="income" onclick="this.form.submit()" <?php if ($_POST['filterOptions'] == 'income') echo 'checked'; ?>>
                    <label class="form-check-label">Income Status</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterOptions" value="medical" onclick="this.form.submit()" <?php if ($_POST['filterOptions'] == 'medical') echo 'checked'; ?>>
                    <label class="form-check-label">Medical Status</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterOptions" value="all" onclick="this.form.submit()" <?php if ($_POST['filterOptions'] == 'all') echo 'checked'; ?>>
                    <label class="form-check-label">Distance</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterOptions" value="eligibility" onclick="this.form.submit()" <?php if ($_POST['filterOptions'] == 'eligibility') echo 'checked'; ?>>
                    <label class="form-check-label">Eligibility</label>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <?php
    // Build the query if all filters are set
    if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender'])) {
        // Convert short course names to full names if needed
        $course_display = $_POST['course'];
        if ($_POST['course'] == "SHS") {
            $course_display = "Bachelor of Science Honours in Speech and Language Therapy";
        } else if ($_POST['course'] == "OT") {
            $course_display = "Bachelor of Science Honours in Occupational Therapy";
        }

        $hostel = "SELECT r.stureg_id, r.studentno, r.distance, (r.m_totincome + r.f_totincome + r.g_totincome) AS totincome, r.medical, r.med_cat, r.siblings, r.m_paysheet_tmp, r.f_paysheet_tmp, r.income_certificate_tmp, r.eligibility 
                    FROM registration r 
                    WHERE r.applying_acayr = '" . $_POST['acayr'] . "' 
                    AND r.batch = '" . $_POST['batch'] . "' 
                    AND r.course = '" . $course_display . "' 
                    AND r.gender = '" . $_POST['gender'] . "' 
                    AND (r.admit IS NULL OR r.admit = '0') 
                    AND r.stureg_id = ( SELECT MAX(stureg_id) FROM registration WHERE studentno = r.studentno ) ";

        // Sorting
        if (!empty($_POST['filterOptions'])) {
            switch ($_POST['filterOptions']) {
                case 'income':      $hostel .= " ORDER BY totincome"; break;
                case 'medical':     $hostel .= " ORDER BY medical DESC"; break;
                case 'all':         $hostel .= " ORDER BY distance DESC"; break;
                case 'eligibility': $hostel .= " ORDER BY eligibility DESC, distance DESC"; break;
                default:            $hostel .= " ORDER BY studentno"; break;
            }
        } else {
            $hostel .= " ORDER BY studentno";
        }

        $hostel_sql = mysqli_query($conn, $hostel);
        $rows = mysqli_num_rows($hostel_sql);
    }
    ?>

    <!-- ===== ACTION FORM (Table + Save / Publish) ===== -->
    <?php if (isset($rows) && $rows > 0): ?>
        <form id="actionForm" action="" method="post">
    <!-- Hidden fields to preserve filter values -->
    <input type="hidden" name="acayr" value="<?php echo $_POST['acayr']; ?>">
    <input type="hidden" name="course" value="<?php echo $_POST['course']; ?>">
    <input type="hidden" name="batch" value="<?php echo $_POST['batch']; ?>">
    <input type="hidden" name="gender" value="<?php echo $_POST['gender']; ?>">
    <input type="hidden" name="filterOptions" value="<?php echo $_POST['filterOptions']; ?>">
    
    <!-- Add this hidden field to confirm publish -->
    <input type="hidden" name="publish_confirm" value="1">

    <!-- table rows... -->

    <div class="form-group" style="text-align:center;">
        <button type="submit" class="btn" style="background:#2F4F4F;color:white;padding:6px;" name="save" value="1">
            Save <i class="fa fa-floppy-o" style="color:white;padding:6px;"></i>
        </button>
        <button type="submit" class="btn" style="background:#2F4F4F;color:white;padding:6px;" name="publish" value="1">
            Publish List <i class="fa fa-cloud-upload" style="color:white;padding:6px;"></i>
        </button>
    </div>
</form>
    <?php else: ?>
        <!-- Optional: show a message when no records match the filters -->
        <div class="alert alert-info">No records found for the selected filters.</div>
    <?php endif; ?>

</div>

<!-- footer -->
<?php include 'footer.php'; ?>

<?php
// ===== SAVE LOGIC =====
if (isset($_POST['save']) && $_POST['save'] == '1') {
    if (isset($rows) && $rows > 0) {
        $save_sql = '';
        for ($i = 1; $i <= $rows; $i++) {
            $el = "eligibility" . $i;
            $eligibility = isset($_POST[$el]) && $_POST[$el] == 1 ? '1' : '0';
            $si = "si" . $i;
            $stureg_id = isset($_POST[$si]) ? $_POST[$si] : 0;
            $save_sql .= "UPDATE registration SET eligibility='$eligibility' WHERE stureg_id='$stureg_id';";
        }
        if (!empty($save_sql)) {
            $run_save = mysqli_multi_query($conn, $save_sql);
            if ($run_save) {
                echo "<script>alert('Your Hostel Student List has been saved successfully!')</script>";
                echo "<meta http-equiv='refresh' content='0'>";
            }
        } else {
            echo "<script>alert('No records to save!')</script>";
        }
    } else {
        echo "<script>alert('No records to save!')</script>";
    }
}

// ===== PUBLISH LOGIC =====
// if (isset($_POST['publish'])) {
if (isset($_POST['publish_confirm']) && $_POST['publish_confirm'] == '1') {
    require 'mail/gmail_api.php';
?>
    <script>
        if (confirm('Have you finalized and saved the list before proceeding?')) {
            <?php
            // Build email lists
            $email1 = '';
            $hostel1 = "SELECT `email` FROM `registration` WHERE `eligibility` = '1' AND `applying_acayr` = '" . $_POST['acayr'] . "' AND `course` = '" . $_POST['course'] . "' AND `batch` = '" . $_POST['batch'] . "' AND `gender` = '" . $_POST['gender'] . "'";
            $hostel_sql1 = mysqli_query($conn, $hostel1);
            if (mysqli_num_rows($hostel_sql1) > 0) {
                while ($row = mysqli_fetch_assoc($hostel_sql1)) {
                    $email1 .= $row['email'] . ",";
                }
                api_sendMail($email1, "", "Hostel Alerts", "You are eligible for hostel accommodation. Kindly proceed with the payment of the hostel fee amounting to Rs. 1,100.00. Please make the payment to the Shroff and upload your receipt through the Hostel Management System (HMS).");
            }

            $email2 = '';
            $hostel2 = "SELECT `email` FROM `registration` WHERE `eligibility` = '0' AND `applying_acayr` = '" . $_POST['acayr'] . "' AND `course` = '" . $_POST['course'] . "' AND `batch` = '" . $_POST['batch'] . "' AND `gender` = '" . $_POST['gender'] . "'";
            $hostel_sql2 = mysqli_query($conn, $hostel2);
            if (mysqli_num_rows($hostel_sql2) > 0) {
                while ($row = mysqli_fetch_assoc($hostel_sql2)) {
                    $email2 .= $row['email'] . ",";
                }
                api_sendMail($email2, "", "Hostel Alerts", "Sorry, you are not eligible for hostel accommodation.");
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