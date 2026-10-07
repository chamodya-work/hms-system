<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}

// Initialize POST variables to avoid undefined index warnings
if (!isset($_POST['acayr']))     $_POST['acayr'] = '';
if (!isset($_POST['course']))    $_POST['course'] = '';
if (!isset($_POST['batch']))     $_POST['batch'] = '';
if (!isset($_POST['studentno'])) $_POST['studentno'] = '';
?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php'; ?>
<div class="container">
    <h2 class="text-center"><br>View Students</h2><br><br>

    <!-- Filters -->
    <form method="post" class="row mb-4">
        <!-- Academic Year -->
        <div class="col-md-3">
            <label for="acayr">Academic Year:</label>
            <select class="form-control" id="acayr" name="acayr" onchange="this.form.submit()">
                <option value="">--Select Academic Year--</option>
                <?php
                // Get distinct academic years from registration (actual applications)
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
        <div class="col-md-3">
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

        <!-- Student No -->
        <div class="col-md-3">
            <label for="studentno">Student No</label>
            <input type="text" class="form-control" id="studentno" name="studentno"
                   value="<?php echo htmlspecialchars($_POST['studentno']); ?>"
                   placeholder="Enter Student No" onchange="this.form.submit()">
        </div>

        <!-- Table -->
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
                    // Build the query with dynamic filters
                    $conditions = [];
                    if (!empty($_POST['acayr'])) {
                        $conditions[] = "r.applying_acayr = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "'";
                    }
                    if (!empty($_POST['course'])) {
                        $conditions[] = "r.course = '" . mysqli_real_escape_string($conn, $_POST['course']) . "'";
                    }
                    if (!empty($_POST['batch'])) {
                        $conditions[] = "r.batch = '" . mysqli_real_escape_string($conn, $_POST['batch']) . "'";
                    }
                    if (!empty($_POST['studentno'])) {
                        $conditions[] = "si.studentno LIKE '%" . mysqli_real_escape_string($conn, $_POST['studentno']) . "%'";
                    }

                    $where = (count($conditions) > 0) ? "WHERE " . implode(" AND ", $conditions) : "";

                    $sql = "SELECT si.studentno, si.name, si.contact, si.email, r.batch, 
                                hb.hos_id, hb.room_no, hb.bed_no
                            FROM student_info si
                            INNER JOIN registration r ON si.studentno = r.studentno
                            LEFT JOIN hostel_bed hb ON r.bed_id = hb.bed_id
                            $where
                            ORDER BY si.studentno";

                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['studentno']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['batch']) . "</td>";
                            // FIXED: Use null coalescing for hostel fields (may be NULL)
                            echo "<td>" . htmlspecialchars($row['hos_id'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['room_no'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['bed_no'] ?? '') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No students found.</td></tr>";
                    }
                    ?>
                </tbody>
        </table>
    </form>
</div>

<?php include 'footer.php'; ?>
</html>