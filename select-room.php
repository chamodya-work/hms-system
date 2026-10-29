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
// 1. INITIALIZE POST VARIABLES
// =========================================================================
if (!isset($_POST['acayr']))   $_POST['acayr'] = '';
if (!isset($_POST['course']))  $_POST['course'] = '';
if (!isset($_POST['batch']))   $_POST['batch'] = '';
if (!isset($_POST['gender']))  $_POST['gender'] = '';

if (isset($_SESSION['keep_post'])) {
    $_POST['acayr']   = $_SESSION['keep_post']['acayr']   ?? '';
    $_POST['course']  = $_SESSION['keep_post']['course']  ?? '';
    $_POST['batch']   = $_SESSION['keep_post']['batch']   ?? '';
    $_POST['gender']  = $_SESSION['keep_post']['gender']  ?? '';
    unset($_SESSION['keep_post']);
}

// Convert acayr ID to year string
$year_str = '';
if (!empty($_POST['acayr'])) {
    $lookup = "SELECT academic_year FROM academic_year WHERE id = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "'";
    $res = mysqli_query($conn, $lookup);
    if ($row = mysqli_fetch_assoc($res)) {
        $year_str = $row['academic_year'];
    }
}

// =========================================================================
// 2. AJAX UNASSIGN BED LOGIC (UPDATED TO RETURN JSON)
// =========================================================================
if (isset($_POST['ajax_unassign_bed'])) {
    $bed_id = intval($_POST['ajax_unassign_bed']);
    
    // 1. Get the student details BEFORE unassigning
    $student_res = mysqli_query($conn, "SELECT stureg_id, studentno FROM registration WHERE bed_id = '$bed_id'");
    $student = mysqli_fetch_assoc($student_res);
    $stu_id = $student['stureg_id'] ?? 0;
    $stu_no = $student['studentno'] ?? '';

    // 2. Remove bed assignment from student & free the bed
    mysqli_query($conn, "UPDATE registration SET bed_id = NULL WHERE bed_id = '$bed_id'");
    mysqli_query($conn, "UPDATE hostel_bed SET availability = '1' WHERE bed_id = '$bed_id'");

    // Get filter data for regenerating dropdown
    $acayr = mysqli_real_escape_string($conn, $_POST['acayr'] ?? '');
    $course = mysqli_real_escape_string($conn, $_POST['course'] ?? '');
    $batch = mysqli_real_escape_string($conn, $_POST['batch'] ?? '');
    $gender = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');

    $year_str_ajax = $acayr;
    if (is_numeric($acayr)) {
        $res = mysqli_query($conn, "SELECT academic_year FROM academic_year WHERE id = '$acayr'");
        if ($row = mysqli_fetch_assoc($res)) $year_str_ajax = $row['academic_year'];
    }

    $course_display = $course;
    if ($course == "SHS") $course_display = "Bachelor of Science Honours in Speech and Language Therapy";
    elseif ($course == "OT") $course_display = "Bachelor of Science Honours in Occupational Therapy";

    // Generate the new dropdown HTML
    $html = '<select class="form-control bed-dropdown" id="' . $bed_id . '" name="' . $bed_id . '">';
    $html .= '<option value="">----</option>';
    
    $eligible_query = "SELECT stureg_id, studentno FROM registration 
                       WHERE applying_acayr = '$year_str_ajax' 
                       AND course = '$course_display' 
                       AND batch = '$batch' 
                       AND gender = '$gender' 
                       AND admit = '1' 
                       AND eligibility = '1'
                       AND (bed_id IS NULL OR bed_id = 0)
                       ORDER BY studentno";
    $eligible_sql = mysqli_query($conn, $eligible_query);
    while ($eligible = mysqli_fetch_assoc($eligible_sql)) {
        $html .= "<option value='{$eligible['stureg_id']}'>{$eligible['studentno']}</option>";
    }
    $html .= '</select>';

    // Return JSON response
    echo json_encode([
        'html' => $html,
        'stu_id' => $stu_id,
        'stu_no' => $stu_no
    ]);
    exit; // Stop script execution for AJAX
}

// =========================================================================
// 3. SAVE BED ASSIGNMENT LOGIC
// =========================================================================
if (isset($_POST['reghos'])) {
    $add_sql1 = '';
    $assigned_students = []; 

    $bedno = "SELECT `bed_id` FROM `hostel_bed` WHERE `availability` = 1; ";
    $bedno_sql = mysqli_query($conn, $bedno);

    while ($bedno_raw = mysqli_fetch_assoc($bedno_sql)) {
        $bed_id = $bedno_raw['bed_id'];

        if (isset($_POST[$bed_id]) && $_POST[$bed_id] != '') {
            $stu = $_POST[$bed_id];

            // Prevent assigning the same student to two beds in one save
            if (!in_array($stu, $assigned_students)) {
                $assigned_students[] = $stu;

                $add_sql1 .= "UPDATE `registration` SET `bed_id` = '$bed_id', `admit` = '1' WHERE `stureg_id` = '$stu'; ";
                $add_sql1 .= "UPDATE `hostel_bed` SET `availability`='0' WHERE `bed_id`='$bed_id'; ";
            }
        }
    }

    if ($add_sql1 !== '') {
        $run_add = mysqli_multi_query($conn, $add_sql1);
        if ($run_add) {
            $_SESSION['success_msg'] = "Hostel details have been successfully updated!";
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

    <form id="addhostel" action="" method="post" class="main-form needs-validation" novalidate>
        <div class="form-row">
            <!-- Filters (Academic Year, Course, Batch, Gender) -->
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
                    while ($row = mysqli_fetch_assoc($acayr_sql)) {
                        $aacayr = $row['acayr'];
                        $academic_year = $row['academic_year'];
                        $selected = ($_POST['acayr'] == $aacayr) ? 'selected' : '';
                        echo "<option value='$aacayr' $selected>$academic_year</option>";
                    }
                    ?>
                </select>
            </div>

            <?php if (!empty($_POST['acayr']) && !empty($year_str)): ?>
                <div class="form-group col-md-3">
                    <label for="course">Course:</label>
                    <select class="form-control" id="course" name="course" onchange="this.form.submit()">
                        <option value="">--Select Course--</option>
                        <?php
                        $course_query = "SELECT DISTINCT course FROM registration WHERE applying_acayr = '" . mysqli_real_escape_string($conn, $year_str) . "' ORDER BY course";
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

            <?php if (!empty($_POST['acayr']) && !empty($_POST['course']) && !empty($year_str)): ?>
                <div class="form-group col-md-3">
                    <label for="batch">Batch:</label>
                    <select class="form-control" id="batch" name="batch" onchange="this.form.submit()">
                        <option value="">--Select Batch--</option>
                        <?php
                        $batch_query = "SELECT DISTINCT batch FROM registration WHERE applying_acayr = '" . mysqli_real_escape_string($conn, $year_str) . "' AND course = '" . mysqli_real_escape_string($conn, $_POST['course']) . "' ORDER BY batch";
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
                            $course_display = $_POST['course'];
                            if ($_POST['course'] == "SHS") $course_display = "Bachelor of Science Honours in Speech and Language Therapy";
                            elseif ($_POST['course'] == "OT") $course_display = "Bachelor of Science Honours in Occupational Therapy";
                            
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
                                    for ($floor = 0; $floor < $floors; $floor++) {
                                        echo '<td style="vertical-align: top; min-width: 250px;"><h6><b>Floor ' . $floor . '</b></h6>';
                                        
                                        // $beds_query = "SELECT r.room_no, r.reserved, r.remark, COUNT(b.bed_id) AS totbeds
                                        //                FROM hostel_room AS r
                                        //                LEFT JOIN hostel_bed AS b ON r.hos_id = b.hos_id AND r.floor_no = b.floor_no AND r.room_no = b.room_no
                                        //                WHERE r.hos_id = '$hos_id' AND r.floor_no = '$floor'
                                        //                GROUP BY r.room_no, r.reserved";

                                        $beds_query = "SELECT r.room_no, r.reserved, r.remark, COUNT(b.bed_id) AS totbeds
                                                        FROM hostel_room AS r
                                                        LEFT JOIN hostel_bed AS b ON r.hos_id = b.hos_id AND r.floor_no = b.floor_no AND r.room_no = b.room_no
                                                        WHERE r.hos_id = '$hos_id' AND r.floor_no = '$floor'
                                                        GROUP BY r.room_no, r.reserved
                                                        ORDER BY CAST(r.room_no AS UNSIGNED) ASC";

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
                                                        $bed_query = "SELECT hb.bed_id, hb.availability, r.studentno, r.applying_acayr 
                                                                    FROM hostel_bed hb 
                                                                    LEFT JOIN registration r ON hb.bed_id = r.bed_id AND (r.applying_acayr = '" . mysqli_real_escape_string($conn, $year_str) . "' OR r.applying_acayr IS NULL) 
                                                                    WHERE hb.hos_id = '$hos_id' AND hb.floor_no = '$floor' AND hb.room_no = '$room_id'";
                                                        $bed_sql = mysqli_query($conn, $bed_query);
                                                        
                                                        while ($bed = mysqli_fetch_assoc($bed_sql)) {
                                                            $bed_id = $bed['bed_id'];
                                                            $availability = $bed['availability'];
                                                            
                                                            if ($availability == 1) {
                                                                ?>
                                                                <!-- Available Bed -->
                                                                <select class="form-control bed-dropdown" id="<?php echo $bed_id; ?>" name="<?php echo $bed_id; ?>">
                                                                    <option value="">----</option>
                                                                    <?php
                                                                    $eligible_query = "SELECT `stureg_id`, `studentno` FROM `registration` 
                                                                                    WHERE `applying_acayr` = '" . mysqli_real_escape_string($conn, $year_str) . "' 
                                                                                    AND `course` = '" . mysqli_real_escape_string($conn, $course_display) . "' 
                                                                                    AND `batch` = '" . mysqli_real_escape_string($conn, $_POST['batch']) . "' 
                                                                                    AND `gender` = '" . mysqli_real_escape_string($conn, $_POST['gender']) . "' 
                                                                                    AND `admit` = '1' 
                                                                                    AND `eligibility` = '1'
                                                                                    AND (`bed_id` IS NULL OR `bed_id` = 0)
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
                                                                // Occupied Bed
                                                                $assigned_query = "SELECT studentno FROM registration WHERE bed_id = '$bed_id' AND applying_acayr = '" . mysqli_real_escape_string($conn, $year_str) . "'";
                                                                $assigned_sql = mysqli_query($conn, $assigned_query);
                                                                $assigned = mysqli_fetch_assoc($assigned_sql);
                                                                $studentno_assigned = $assigned['studentno'] ?? '';
                                                                ?>
                                                                <div id="bed-container-<?php echo $bed_id; ?>" style="display:flex; gap:5px;">
                                                                    <input type="text" class="form-control" value="<?php echo $studentno_assigned; ?>" disabled />
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="unassignBed(<?php echo $bed_id; ?>, '<?php echo $studentno_assigned; ?>')" title="Unassign Bed"><i class="fa fa-times"></i></button>
                                                                </div>
                                                                <?php
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
    // 1. Function to hide selected students from other dropdowns
    function updateDropdowns() {
        const dropdowns = document.querySelectorAll('.bed-dropdown');
        const selectedValues = [];
        
        // Gather all currently selected student IDs
        dropdowns.forEach(select => {
            if (select.value !== "") {
                selectedValues.push(select.value);
            }
        });

        // Hide options that are selected elsewhere
        dropdowns.forEach(select => {
            const options = select.options;
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (option.value === "") continue;
                
                if (selectedValues.includes(option.value) && select.value !== option.value) {
                    option.hidden = true; 
                } else {
                    option.hidden = false; 
                }
            }
        });
    }

    // 2. AJAX Unassign Function
    function unassignBed(bedId, studentName) {
        if (!confirm('Are you sure you want to unassign ' + studentName + ' from this bed?')) return;

        const formData = new URLSearchParams();
        formData.append('ajax_unassign_bed', bedId);
        formData.append('acayr', document.getElementById('acayr').value);
        formData.append('course', document.getElementById('course').value);
        formData.append('batch', document.getElementById('batch').value);
        formData.append('gender', document.getElementById('gender').value);

        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            // Replace the grey box with the new dropdown
            const container = document.getElementById('bed-container-' + bedId);
            container.outerHTML = data.html;

            // Add the newly unassigned student to ALL other dropdowns (if they don't exist)
            if (data.stu_id > 0) {
                document.querySelectorAll('.bed-dropdown').forEach(select => {
                    if (select.id !== bedId.toString()) {
                        let exists = false;
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value === data.stu_id.toString()) {
                                exists = true;
                                break;
                            }
                        }
                        if (!exists) {
                            select.add(new Option(data.stu_no, data.stu_id));
                        }
                    }
                });
            }

            // Attach event listener to new dropdown
            const newSelect = document.getElementById(bedId);
            newSelect.addEventListener('change', updateDropdowns);

            // Update visibility
            updateDropdowns();
        })
        .catch(error => {
            console.error(error);
            alert('Error unassigning bed. Please try again.');
        });
    }

    // 3. Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.bed-dropdown').forEach(select => {
            select.addEventListener('change', updateDropdowns);
        });
        updateDropdowns();
    });
</script>
</body>
</html>