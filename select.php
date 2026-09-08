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

// DEBUG MODE: set to true to show debug output instead of refreshing
$debug = false; // change to false when everything works

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<!-- header (this includes the database connection) -->
<?php include 'header.php'; ?>

<?php
// ===== Convert acayr ID to actual year string (now $conn is available) =====
$acayr_id = isset($_POST['acayr']) ? $_POST['acayr'] : '';
$year_str = '';
if (!empty($acayr_id)) {
    $lookup = "SELECT academic_year FROM academic_year WHERE id = '" . mysqli_real_escape_string($conn, $acayr_id) . "'";
    $res = mysqli_query($conn, $lookup);
    if ($row = mysqli_fetch_assoc($res)) {
        $year_str = $row['academic_year'];
    }
}
?>

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
                    $acayr_query = "SELECT MAX(hr.acayr) AS acayr, ay.academic_year 
                                    FROM hostel_reg hr
                                    INNER JOIN academic_year ay ON hr.acayr = ay.id
                                    WHERE hr.acayr != '0'
                                    GROUP BY ay.academic_year
                                    ORDER BY ay.academic_year DESC";
                    $acayr_sql = mysqli_query($conn, $acayr_query);
                    while ($acayr_raw = mysqli_fetch_assoc($acayr_sql)) {
                        $aacayr = $acayr_raw['acayr'];
                        $ayear = $acayr_raw['academic_year'];
                    ?>
                        <option value="<?php echo $aacayr; ?>" <?php if ($_POST['acayr'] == $aacayr) echo 'selected'; ?>>
                            <?php echo $ayear; ?>
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
                        // Use $year_str (now defined)
                        $course = "SELECT DISTINCT course FROM registration WHERE applying_acayr = '" . mysqli_real_escape_string($conn, $year_str) . "' ORDER BY course";
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
                        $batch = "SELECT DISTINCT batch FROM registration WHERE applying_acayr = '" . mysqli_real_escape_string($conn, $year_str) . "' AND course='" . mysqli_real_escape_string($conn, $_POST['course']) . "' ORDER BY batch";
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

    <!-- ===== SORT RADIO BUTTONS ===== -->
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
    // ===== BUILD QUERY =====
    if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender'])) {
        $course_display = $_POST['course'];
        if ($_POST['course'] == "SHS") {
            $course_display = "Bachelor of Science Honours in Speech and Language Therapy";
        } else if ($_POST['course'] == "OT") {
            $course_display = "Bachelor of Science Honours in Occupational Therapy";
        }

        $hostel = "SELECT r.stureg_id, r.studentno, r.distance, (r.m_totincome + r.f_totincome + r.g_totincome) AS totincome, r.medical, r.med_cat, r.siblings, r.m_paysheet_tmp, r.f_paysheet_tmp, r.income_certificate_tmp, r.eligibility 
                    FROM registration r 
                    WHERE r.applying_acayr = '" . mysqli_real_escape_string($conn, $year_str) . "' 
                    AND r.batch = '" . mysqli_real_escape_string($conn, $_POST['batch']) . "' 
                    AND r.course = '" . mysqli_real_escape_string($conn, $course_display) . "' 
                    AND r.gender = '" . mysqli_real_escape_string($conn, $_POST['gender']) . "' 
                    AND (r.admit IS NULL OR r.admit = '0') 
                    AND r.stureg_id = ( SELECT MAX(stureg_id) FROM registration WHERE studentno = r.studentno ) ";

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
            <input type="hidden" name="publish_confirm" value="1">

            <div class="form-group">
                <table class="table table-hover" style="width:75%;margin:auto;">
                    <thead>
                        <tr>
                            <th>Student No</th>
                            <th>Distance</th>
                            <th>Income (Rs.)</th>
                            <th>Medical</th>
                            <th>Siblings</th>
                            <th>View Files</th>
                            <th>Eligibility</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        while ($hostel_raw = mysqli_fetch_assoc($hostel_sql)) {
                            $stureg_id = $hostel_raw['stureg_id'];
                            $studentno = $hostel_raw['studentno'];
                            $distance = $hostel_raw['distance'];
                            $medical = ($hostel_raw['medical'] == 1) ? "Yes, " . $hostel_raw['med_cat'] : "-";
                            $siblings = ($hostel_raw['siblings'] == 1) ? "Yes" : "-";
                            $totincome = $hostel_raw['totincome'];
                            $eligibility = $hostel_raw['eligibility'];
                            $m_pay = $hostel_raw['m_paysheet_tmp'];
                            $f_pay = $hostel_raw['f_paysheet_tmp'];
                            $income_cert = $hostel_raw['income_certificate_tmp'];
                            $i++;
                        ?>
                            <tr>
                                <td>
                                    <input type="text" name="studentno<?php echo $i; ?>" value="<?php echo $studentno; ?>" hidden>
                                    <?php echo $studentno; ?>
                                </td>
                                <td><?php echo $distance; ?> km</td>
                                <td style="text-align:right;"><?php echo number_format($totincome, 2, '.', ','); ?></td>
                                <td><?php echo $medical; ?></td>
                                <td><?php echo $siblings; ?></td>
                                <td>
                                    <?php if ($m_pay) echo "<a target='_blank' href='https://hosmed.kln.ac.lk/mail/tmp_files/$m_pay'>Mother's paysheet</a><br>"; ?>
                                    <?php if ($f_pay) echo "<a target='_blank' href='https://hosmed.kln.ac.lk/mail/tmp_files/$f_pay'>Father's paysheet</a><br>"; ?>
                                    <?php if ($income_cert) echo "<a target='_blank' href='https://hosmed.kln.ac.lk/mail/tmp_files/$income_cert'>Grama Niladari Certificate</a>"; ?>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" name="eligibility<?php echo $i; ?>" <?php echo ($eligibility == 1) ? 'checked' : ''; ?>>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

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
        <div class="alert alert-info">No records found for the selected filters.</div>
    <?php endif; ?>

</div>

<!-- footer -->
<?php include 'footer.php'; ?>

<?php
// =============================================
// ===== SAVE LOGIC (UPDATED – uses studentno and $year_str) =====
// =============================================
if (isset($_POST['save']) && $_POST['save'] == '1') {
    if ($debug) {
        echo "<h3>Save button clicked!</h3>";
        echo "<pre>POST DATA: " . print_r($_POST, true) . "</pre>";
        echo "<pre>Rows variable: " . (isset($rows) ? $rows : 'NOT SET') . "</pre>";
    }

    if (isset($rows) && $rows > 0) {
        $acayr_str = $year_str; 
        $save_sql = '';
        $update_count = 0;

        for ($i = 1; $i <= $rows; $i++) {
            $el = "eligibility" . $i;
            $eligibility = isset($_POST[$el]) && $_POST[$el] == 1 ? '1' : '0';
            $studentno_key = "studentno" . $i;
            $studentno = isset($_POST[$studentno_key]) ? $_POST[$studentno_key] : '';

            if (!empty($studentno)) {
                $save_sql .= "UPDATE registration SET eligibility='$eligibility' WHERE studentno='" . mysqli_real_escape_string($conn, $studentno) . "' AND applying_acayr='" . mysqli_real_escape_string($conn, $acayr_str) . "';";
                $update_count++;
                if ($debug) echo "<p>i=$i, studentno=$studentno, eligibility=$eligibility</p>";
            } else {
                if ($debug) echo "<p>i=$i: studentno not set</p>";
            }
        }

        if (!empty($save_sql)) {
            if ($debug) echo "<pre>SAVE SQL: " . htmlspecialchars($save_sql) . "</pre>";

            $run_save = mysqli_multi_query($conn, $save_sql);
            if (!$run_save) {
                echo "MySQL Error: " . mysqli_error($conn);
            } else {
                if ($debug) {
                    echo "Query executed successfully! Updated $update_count record(s).";
                } else {
                    echo "<script>alert('Your Hostel Student List has been saved successfully!')</script>";
                    echo "<meta http-equiv='refresh' content='0'>";
                }
            }
        } else {
            echo "<script>alert('No records to save!')</script>";
        }
    } else {
        echo "<script>alert('No records to save!')</script>";
    }

    if ($debug) exit;
}

// =============================================
// ===== PUBLISH LOGIC (FIXED – uses $year_str) =====
// =============================================
if (isset($_POST['publish']) && $_POST['publish'] == '1') {
    require 'mail/gmail_api.php';

    if (!isset($_POST['confirmed']) || $_POST['confirmed'] != '1') {
        ?>
        <script>
            if (confirm('Have you finalized and saved the list before proceeding?')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                <?php
                foreach ($_POST as $key => $value) {
                    echo "var input = document.createElement('input'); input.type = 'hidden'; input.name = '$key'; input.value = '".addslashes($value)."'; form.appendChild(input);\n";
                }
                ?>
                var input = document.createElement('input'); input.type = 'hidden'; input.name = 'confirmed'; input.value = '1'; form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Action cancelled! No emails were sent.');
            }
        </script>
        <?php
        exit;
    }

    $email1 = '';
    $hostel1 = "SELECT `email` FROM `registration` WHERE `eligibility` = '1' AND `applying_acayr` = '" . mysqli_real_escape_string($conn, $year_str) . "' AND `course` = '" . mysqli_real_escape_string($conn, $_POST['course']) . "' AND `batch` = '" . mysqli_real_escape_string($conn, $_POST['batch']) . "' AND `gender` = '" . mysqli_real_escape_string($conn, $_POST['gender']) . "'";
    $hostel_sql1 = mysqli_query($conn, $hostel1);
    if (mysqli_num_rows($hostel_sql1) > 0) {
        while ($row = mysqli_fetch_assoc($hostel_sql1)) {
            $email1 .= $row['email'] . ",";
        }
        $email1 = rtrim($email1, ',');
    }

    $email2 = '';
    $hostel2 = "SELECT `email` FROM `registration` WHERE `eligibility` = '0' AND `applying_acayr` = '" . mysqli_real_escape_string($conn, $year_str) . "' AND `course` = '" . mysqli_real_escape_string($conn, $_POST['course']) . "' AND `batch` = '" . mysqli_real_escape_string($conn, $_POST['batch']) . "' AND `gender` = '" . mysqli_real_escape_string($conn, $_POST['gender']) . "'";
    $hostel_sql2 = mysqli_query($conn, $hostel2);
    if (mysqli_num_rows($hostel_sql2) > 0) {
        while ($row = mysqli_fetch_assoc($hostel_sql2)) {
            $email2 .= $row['email'] . ",";
        }
        $email2 = rtrim($email2, ',');
    }

    $sent_count = 0;
    if (!empty($email1)) {
        api_sendMail("chamodyarajapaksha1@gmail.com", "", "Hostel Alerts", "You are eligible for hostel accommodation. Kindly proceed with the payment of the hostel fee amounting to Rs. 1,100.00. Please make the payment to the Shroff and upload your receipt through the Hostel Management System (HMS).");
        $sent_count++;
    }
    if (!empty($email2)) {
        api_sendMail("chamodyarajapaksha1@gmail.com", "", "Hostel Alerts", "Sorry, you are not eligible for hostel accommodation.");
        $sent_count++;
    }

    ?>
    <script>
        alert('Emails have been sent successfully (<?php echo $sent_count; ?> batch(es))!');
        window.location.href = window.location.href;
    </script>
    <?php
    exit;
}
?>
</body>
</html>