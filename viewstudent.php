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
    <h2 class="text-center"><br>View Students</h2><br><br>

    <!-- Filters -->
    <form method="post" class="row mb-4">
        <div class="col-md-3">
            <label for="acayr">Academic Year:</label>
            <select class="form-control" id="acayr" name="acayr" onchange="submit()">
                <option value="">--Select Academic Year--</option>
                <?php
                $acayr = "SELECT acayr,academic_year FROM hostel_reg hr INNER JOIN academic_year ay ON hr.acayr=ay.id WHERE acayr!='0' GROUP BY acayr ORDER BY acayr DESC";
                $acayr_sql = mysqli_query($conn, $acayr);
                while ($acayr_raw = mysqli_fetch_assoc($acayr_sql)) {
                    $aacayr = $acayr_raw['acayr'];
                    $academic_year = $acayr_raw['academic_year'];
                    ?>
                    <option value="<?php echo $aacayr; ?>" <?php if (isset($_POST['acayr'])) {
                           echo ($_POST['acayr'] == $aacayr) ? 'selected' : '';
                       } ?>>
                        <?php echo $academic_year; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="course">Course:</label>
            <select class="form-control" id="course" name="course" onchange="submit()">
                <option value="">--Select Course--</option>
                <?php
                if (isset($_POST['acayr']) AND ($_POST['acayr']) != null) {
                    $course = "SELECT course FROM hostel_reg WHERE acayr = '" . $_POST['acayr'] . "' GROUP BY course ORDER BY course";
                    $course_sql = mysqli_query($conn, $course);
                    while ($course_raw = mysqli_fetch_assoc($course_sql)) {
                        $acourse = $course_raw['course'];
                        ?>
                        <option value="<?php echo $acourse; ?>" <?php if (isset($_POST['course'])) {
                               echo ($_POST['course'] == $acourse) ? 'selected' : '';
                           } ?>>
                            <?php echo $acourse; ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="batch">Batch:</label>
            <select class="form-control" id="batch" name="batch" onchange="submit()">
                <option value="">--Select Batch--</option>
                <?php
                if (isset($_POST['course']) AND ($_POST['course']) != null) {
                    $batch = "SELECT batch FROM hostel_reg WHERE acayr = '" . $_POST['acayr'] . "' AND course = '" . $_POST['course'] . "' GROUP BY batch ORDER BY batch";
                    $batch_sql = mysqli_query($conn, $batch);
                    while ($batch_raw = mysqli_fetch_assoc($batch_sql)) {
                        $abatch = $batch_raw['batch'];
                        ?>
                        <option value="<?php echo $abatch; ?>" <?php if (isset($_POST['batch'])) {
                               echo ($_POST['batch'] == $abatch) ? 'selected' : '';
                           } ?>>
                            <?php echo $abatch; ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="batch">Student No</label>
            <input type="text" class="form-control" id="studentno" name="studentno"
                   value="<?php echo isset($_POST['studentno']) ? htmlspecialchars($_POST['studentno']) : ''; ?>"
                   placeholder="Enter Student No" onchange="submit()">
        </div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Student No</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Batch</th>
                    <th>Hostel</th>
                    <th>Room No.</th>
                    <th>Bed No.</th>
                </tr>
            </thead>
            <tbody>
                <?php
        $studentCriteria = "";
                if (isset($_POST['studentno']) AND ($_POST['studentno']) != null) {
                    $studentCriteria = "AND si.studentno LIKE '%" . (isset($_POST['studentno']) ? $_POST['studentno'] : '') . "%'";
    
                }
                $sql = "SELECT * FROM student_info si INNER JOIN registration r ON si.studentno = r.studentno  INNER JOIN studreg_bed sb ON sb.stureg_id=r.stureg_id INNER JOIN hostel_bed hb ON hb.bed_id=sb.bed_id WHERE r.applying_acayr=(SELECT academic_year FROM academic_year WHERE is_current=1)
            AND r.course='" . $_POST['course'] . "' AND r.batch='" . $_POST['batch'] . "' " . $studentCriteria . " ORDER BY si.studentno";

                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['studentno'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['contact'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row["batch"] . "</td>";
                        echo "<td>" . $row["hos_id"] . "</td>";
                        echo "<td>" . $row["room_no"] . "</td>";
                        echo "<td>" . $row["bed_no"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
</div>




</div>

<?php include 'footer.php'; ?>

</html>