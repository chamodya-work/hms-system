<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}

include("connection/connect.php");
date_default_timezone_set("Asia/Colombo");

// =========================================================================
// 1. INITIALIZE POST VARIABLES (to avoid undefined warnings)
// =========================================================================
if (!isset($_POST['acayr']))   $_POST['acayr'] = '';
if (!isset($_POST['course']))  $_POST['course'] = '';
if (!isset($_POST['batch']))   $_POST['batch'] = '';
if (!isset($_POST['gender']))  $_POST['gender'] = '';

// =========================================================================
// 2. RESTORE STATE AFTER REDIRECT (session restoration)
// =========================================================================
if (isset($_SESSION['keep_post'])) {
    $_POST['acayr']   = $_SESSION['keep_post']['acayr']   ?? '';
    $_POST['course']  = $_SESSION['keep_post']['course']  ?? '';
    $_POST['batch']   = $_SESSION['keep_post']['batch']   ?? '';
    $_POST['gender']  = $_SESSION['keep_post']['gender']  ?? '';
    unset($_SESSION['keep_post']);
}

// =========================================================================
// 3. PROCESS FORM SUBMISSION – NO studreg_bed; update registration directly
// =========================================================================
if (isset($_POST['reghos'])) {
    $add_sql1 = '';

    // Get all available beds
    $bedno = "SELECT `bed_id` FROM `hostel_bed` WHERE `availability` = 1; ";
    $bedno_sql = mysqli_query($conn, $bedno);

    while ($bedno_raw = mysqli_fetch_assoc($bedno_sql)) {
        $bed_id = $bedno_raw['bed_id'];

        // If a student was selected for this bed (the dropdown is named by bed_id)
        if (isset($_POST[$bed_id]) && $_POST[$bed_id] != '') {
            $stu = $_POST[$bed_id];

            // Update registration: assign bed and mark as admitted (admit = 1)
            // Also free the bed in hostel_bed
            $add_sql1 .= "UPDATE `registration` SET `bed_id` = '$bed_id', `admit` = '1' WHERE `stureg_id` = '$stu'; ";
            $add_sql1 .= "UPDATE `hostel_bed` SET `availability`='0' WHERE `bed_id`='$bed_id'; ";
        }
    }

    if ($add_sql1 !== '') {
        $run_add = mysqli_multi_query($conn, $add_sql1);
        if ($run_add) {
            $_SESSION['success_msg'] = "Hostel details have been successfully updated!";
            // Save filter states for reload
            $_SESSION['keep_post'] = [
                'acayr'  => $_POST['acayr']  ?? '',
                'course' => $_POST['course'] ?? '',
                'batch'  => $_POST['batch']  ?? '',
                'gender' => $_POST['gender'] ?? ''
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php'; ?>
<div class="container">
    <h2 class="text-center"><br>Room Admission</h2><br><br>

    <!--Form starts here-->
    <form id="addhostel" action="" method="post" class="main-form needs-validation" novalidate>
        <div class="form-row">
            <!-- Academic Year -->
            <div class="form-group col-md-3">
                <label for="acayr">Academic Year:</label>
                <select class="form-control" id="acayr" name="acayr" onchange="this.form.submit()">
                    <option value="">--Select Academic Year--</option>
                    <?php
                    $acayr_query = "SELECT academic_year, id FROM academic_year";
                    $acayr_sql = mysqli_query($conn, $acayr_query);
                    while ($row = mysqli_fetch_assoc($acayr_sql)) {
                        $aacayr = $row['id'];
                        $academic_year = $row['academic_year'];
                        $selected = ($_POST['acayr'] == $academic_year) ? 'selected' : '';
                        echo "<option value='$academic_year' $selected>$academic_year</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Course – using IN to handle possible duplicate IDs for same academic year -->
            <?php if (!empty($_POST['acayr'])): ?>
                <div class="form-group col-md-3">
                    <label for="course">Course:</label>
                    <select class="form-control" id="course" name="course" onchange="this.form.submit()">
                        <option value="">--Select Course--</option>
                        <?php
                        // CHANGED: use IN instead of = for acayr (acayr is academic_year.id)
                        $course_query = "SELECT course FROM hostel_reg 
                                         WHERE acayr IN (SELECT id FROM academic_year WHERE academic_year = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "') 
                                         GROUP BY course ORDER BY course";
                        $course_sql = mysqli_query($conn, $course_query);
                        while ($row = mysqli_fetch_assoc($course_sql)) {
                            $acourse = $row['course'];
                            $selected = ($_POST['course'] == $acourse) ? 'selected' : '';
                            echo "<option value='$acourse' $selected>$acourse</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Batch – also using IN -->
            <?php if (!empty($_POST['acayr']) && !empty($_POST['course'])): ?>
                <div class="form-group col-md-3">
                    <label for="batch">Batch:</label>
                    <select class="form-control" id="batch" name="batch" onchange="this.form.submit()">
                        <option value="">--Select Batch--</option>
                        <?php
                        // CHANGED: use IN
                        $batch_query = "SELECT batch FROM hostel_reg 
                                        WHERE acayr IN (SELECT id FROM academic_year WHERE academic_year = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "') 
                                        AND course='" . mysqli_real_escape_string($conn, $_POST['course']) . "' 
                                        ORDER BY batch";
                        $batch_sql = mysqli_query($conn, $batch_query);
                        while ($row = mysqli_fetch_assoc($batch_sql)) {
                            $abatch = $row['batch'];
                            $selected = ($_POST['batch'] == $abatch) ? 'selected' : '';
                            echo "<option value='$abatch' $selected>$abatch</option>";
                        }
                        ?>
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

        <!-- ===== Main table: Hostel rooms with beds ===== -->
        <?php if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch']) && !empty($_POST['gender'])): ?>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <table class="table table-hover" style="width:100%; margin:0px;">
                        <thead>
                            <tr>
                                <th style="width:10%;">Hostel</th>
                                <th>Rooms</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get hostels for the selected gender
                            $hostel_query = "SELECT `hos_id`, hos_floors FROM `hostel` WHERE hos_id != '0' AND gender = '" . mysqli_real_escape_string($conn, $_POST['gender']) . "' ORDER BY hos_id ASC";
                            $hostel_sql = mysqli_query($conn, $hostel_query);

                            while ($hostel_row = mysqli_fetch_assoc($hostel_sql)) {
                                $hos_id = $hostel_row['hos_id'];
                                $floors = $hostel_row['hos_floors'];
                                ?>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="<?php echo $hos_id; ?>" value="<?php echo $hos_id; ?>" readonly>
                                    </td>
                                    <?php
                                    // Loop through each floor
                                    for ($floor = 0; $floor < $floors; $floor++) {
                                        echo '<td><h6><b>Floor ' . $floor . '</b></h6>';

                                        // Get rooms on this floor
                                        $beds_query = "SELECT r.room_no, r.reserved, r.remark, COUNT(b.bed_id) AS totbeds
                                                       FROM hostel_room AS r
                                                       LEFT JOIN hostel_bed AS b ON r.hos_id = b.hos_id AND r.floor_no = b.floor_no AND r.room_no = b.room_no
                                                       WHERE r.hos_id = '$hos_id' AND r.floor_no = '$floor'
                                                       GROUP BY r.room_no, r.reserved";
                                        $beds_sql = mysqli_query($conn, $beds_query);

                                        echo "<table>";
                                        while ($bed_row = mysqli_fetch_assoc($beds_sql)) {
                                            $room_id = $bed_row['room_no'];
                                            $reserved = $bed_row['reserved'];
                                            $remark = $bed_row['remark'];
                                            ?>
                                            <tr>
                                                <td><label class="form-check-label"><?php echo $room_id; ?></label>&nbsp;</td>
                                                <td>
                                                    <?php if ($reserved == 0): ?>
                                                        <?php
                                                        // Get beds for this room
                                                        $bed_query = "SELECT hb.bed_id, hb.availability, r.studentno, r.applying_acayr 
                                                                      FROM hostel_bed hb 
                                                                      LEFT JOIN registration r ON hb.bed_id = r.bed_id AND (r.applying_acayr = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "' OR r.applying_acayr IS NULL) 
                                                                      WHERE hb.hos_id = '$hos_id' AND hb.floor_no = '$floor' AND hb.room_no = '$room_id'";
                                                        $bed_sql = mysqli_query($conn, $bed_query);
                                                        while ($bed = mysqli_fetch_assoc($bed_sql)) {
                                                            $bed_id = $bed['bed_id'];
                                                            $availability = $bed['availability'];
                                                            $stuno = $bed['studentno'];
                                                            if ($availability == 1) {
                                                                ?>
                                                                <!-- Available bed – show dropdown of eligible students -->
                                                                <select class="form-control" id="<?php echo $bed_id; ?>" name="<?php echo $bed_id; ?>">
                                                                    <option value="">----</option>
                                                                    <?php
                                                                    $eligible_query = "SELECT `stureg_id`, `studentno` FROM `registration` 
                                                                                       WHERE `applying_acayr` = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "' 
                                                                                       AND `course` = '" . mysqli_real_escape_string($conn, $_POST['course']) . "' 
                                                                                       AND `batch` = '" . mysqli_real_escape_string($conn, $_POST['batch']) . "' 
                                                                                       AND `gender` = '" . mysqli_real_escape_string($conn, $_POST['gender']) . "' 
                                                                                       AND `admit` = '0' 
                                                                                       ORDER BY studentno";
                                                                    $eligible_sql = mysqli_query($conn, $eligible_query);
                                                                    while ($eligible = mysqli_fetch_assoc($eligible_sql)) {
                                                                        $stureg_id = $eligible['stureg_id'];
                                                                        $studentno = $eligible['studentno'];
                                                                        $selected = (isset($_POST[$bed_id]) && $_POST[$bed_id] == $stureg_id) ? 'selected' : '';
                                                                        echo "<option value='$stureg_id' $selected>$studentno</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <?php
                                                            } else {
                                                                // Bed is already occupied – retrieve the assigned student from registration
                                                                // CHANGED: removed studreg_bed, use registration.bed_id directly
                                                                $assigned_query = "SELECT studentno 
                                                                                   FROM registration 
                                                                                   WHERE bed_id = '$bed_id' 
                                                                                   AND applying_acayr = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "'";
                                                                $assigned_sql = mysqli_query($conn, $assigned_query);
                                                                $assigned = mysqli_fetch_assoc($assigned_sql);
                                                                $studentno_assigned = $assigned['studentno'] ?? '';
                                                                echo "<input type='text' class='form-control' value='$studentno_assigned' disabled />";
                                                            }
                                                        }
                                                        ?>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($remark); ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        echo "</table>";
                                        echo '</td>';
                                    }
                                    ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group" style="text-align:center;">
                <button type="submit" class="btn btn-primary" id="reghos" name="reghos">Save</button>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Please select all filters (Academic Year, Course, Batch, Gender) to see rooms.</div>
        <?php endif; ?>
    </form>
</div>
<!-- footer -->
<?php include 'footer.php'; ?>

<!-- JavaScript -->
<script>
    var form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function (event) {
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