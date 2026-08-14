<?php
// Initialize the session
session_start();
include("connection/connect.php");

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}

// Initialize POST variables to avoid undefined index warnings
if (!isset($_POST['filter']))   $_POST['filter'] = '';
if (!isset($_POST['acayr']))    $_POST['acayr'] = '';
if (!isset($_POST['course']))   $_POST['course'] = '';
if (!isset($_POST['batch']))    $_POST['batch'] = '';
if (!isset($_POST['gender']))   $_POST['gender'] = '';
if (!isset($_POST['hostel']))   $_POST['hostel'] = '';

// Handle delete action (comes from GET)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql = "SELECT bed_id FROM registration WHERE stureg_id = $id;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_bed_id = $row['bed_id'];
        // Remove bed assignment and free the bed
        // $sql2 = "UPDATE `registration` SET `bed_id`='0' WHERE stureg_id = $id;
        //          UPDATE `hostel_bed` SET `availability`='1' WHERE bed_id = $current_bed_id";

        $sql2 = "UPDATE `registration` SET `bed_id` = NULL, `admit` = '0' WHERE stureg_id = $id;
                UPDATE `hostel_bed` SET `availability`='1' WHERE bed_id = $current_bed_id";

        if ($conn->multi_query($sql2) === TRUE) {
            echo "<script>alert('Bed has been successfully removed!')</script>";
            echo "<script> window.location =  'viewcurrent.php' ; </script>";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    exit;
}
?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php include 'header.php'; ?>
<div class="container">
    <h2 class="text-center"><br>Current Student List</h2><br><br>

    <!--Form starts here-->
    <form id="hoslist" action="" method="post" class="main-form">
        <div class="form-row">
            <!-- Filter selection -->
            <div class="form-group col-md-3">
                <label for="filter">Filter By:</label>
                <select class="form-control" id="filter" name="filter" onchange="this.form.submit()">
                    <option value="" <?php if ($_POST['filter'] == '') echo 'selected'; ?>>-- Select --</option>
                    <option value="hos" <?php if ($_POST['filter'] == 'hos') echo 'selected'; ?>>Hostel</option>
                    <option value="other" <?php if ($_POST['filter'] == 'other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <?php if (!empty($_POST['filter'])): ?>
                <!-- Academic Year -->
                <div class="form-group col-md-3">
                    <label for="acayr">Academic Year:</label>
                    <select class="form-control" id="acayr" name="acayr" onchange="this.form.submit()">
                        <option value="">--Select Academic Year--</option>
                        <?php
                        $acayr_query = "SELECT DISTINCT applying_acayr FROM registration ORDER BY applying_acayr DESC LIMIT 4";
                        $acayr_sql = mysqli_query($conn, $acayr_query);
                        while ($row = mysqli_fetch_assoc($acayr_sql)) {
                            $aacayr = $row['applying_acayr'];
                            $selected = ($_POST['acayr'] == $aacayr) ? 'selected' : '';
                            echo "<option value='$aacayr' $selected>$aacayr</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Course (only for 'other' filter) -->
                <?php if ($_POST['filter'] == 'other' && !empty($_POST['acayr'])): ?>
                    <div class="form-group col-md-3">
                        <label for="course">Course:</label>
                        <select class="form-control" id="course" name="course" onchange="this.form.submit()">
                            <option value="">--Select Course--</option>
                            <?php
                            $course_query = "SELECT DISTINCT course FROM registration WHERE applying_acayr = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "' ORDER BY course";
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

                <!-- Batch (only for 'other' filter) -->
                <?php if ($_POST['filter'] == 'other' && !empty($_POST['acayr']) && !empty($_POST['course'])): ?>
                    <div class="form-group col-md-3">
                        <label for="batch">Batch:</label>
                        <select class="form-control" id="batch" name="batch" onchange="this.form.submit()">
                            <option value="">--Select Batch--</option>
                            <?php
                            $batch_query = "SELECT DISTINCT batch FROM registration WHERE applying_acayr = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "' AND course = '" . mysqli_real_escape_string($conn, $_POST['course']) . "' ORDER BY batch DESC";
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

                <!-- Gender (only for 'other' filter) -->
                <?php if ($_POST['filter'] == 'other' && !empty($_POST['acayr']) && !empty($_POST['course']) && !empty($_POST['batch'])): ?>
                    <div class="form-group col-md-3">
                        <label for="gender">Gender:</label>
                        <select class="form-control" id="gender" name="gender" onchange="this.form.submit()">
                            <option value="">--Select Gender--</option>
                            <option value="m" <?php if ($_POST['gender'] == 'm') echo 'selected'; ?>>Male</option>
                            <option value="f" <?php if ($_POST['gender'] == 'f') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Hostel (only for 'hos' filter) -->
                <?php if ($_POST['filter'] == 'hos' && !empty($_POST['acayr'])): ?>
                    <div class="form-group col-md-3">
                        <label for="hostel">Hostel:</label>
                        <select class="form-control" id="hostel" name="hostel" onchange="this.form.submit()">
                            <option value="">-- Select --</option>
                            <?php
                            $hostel_query = "SELECT hos_id FROM hostel WHERE hos_id != '0' ORDER BY hos_id";
                            $hostel_sql = mysqli_query($conn, $hostel_query);
                            while ($row = mysqli_fetch_assoc($hostel_sql)) {
                                $ahostel = $row['hos_id'];
                                $selected = ($_POST['hostel'] == $ahostel) ? 'selected' : '';
                                echo "<option value='$ahostel' $selected>$ahostel</option>";
                            }
                            ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php
        // ===== Build the filter condition for the query =====
        $conditions = [];

        // Only add conditions if filter is set
        if (!empty($_POST['filter'])) {
            if ($_POST['filter'] == 'other') {
                // Other: use academic year, course, batch, gender
                if (!empty($_POST['acayr'])) {
                    $conditions[] = "applying_acayr = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "'";
                }
                if (!empty($_POST['course'])) {
                    $conditions[] = "course = '" . mysqli_real_escape_string($conn, $_POST['course']) . "'";
                }
                if (!empty($_POST['batch'])) {
                    $conditions[] = "batch = '" . mysqli_real_escape_string($conn, $_POST['batch']) . "'";
                }
                if (!empty($_POST['gender'])) {
                    $conditions[] = "gender = '" . mysqli_real_escape_string($conn, $_POST['gender']) . "'";
                }
            } elseif ($_POST['filter'] == 'hos') {
                // Hostel: use hostel ID and academic year (if selected)
                if (!empty($_POST['hostel'])) {
                    $conditions[] = "hos_id = '" . mysqli_real_escape_string($conn, $_POST['hostel']) . "'";
                }
                if (!empty($_POST['acayr'])) {
                    $conditions[] = "applying_acayr = '" . mysqli_real_escape_string($conn, $_POST['acayr']) . "'";
                }
            }
        }

        $where = (count($conditions) > 0) ? "WHERE " . implode(" AND ", $conditions) : "";
        $order_by = " ORDER BY studentno";

        // Only query if we have at least one condition
        if (!empty($where)) {
            $hostel_query = "SELECT * FROM current_list " . $where . $order_by;
            $hostel_sql = mysqli_query($conn, $hostel_query);
            $rows = mysqli_num_rows($hostel_sql);
        } else {
            $rows = 0;
        }
        ?>

        <?php if (isset($rows) && $rows > 0): ?>
            <div class="form-group">
                <table class="table table-hover" style="width:75%;margin:auto;">
                    <thead>
                        <tr>
                            <th>Student No</th>
                            <th>Hostel</th>
                            <th>Bed No (Floor-Room-Bed)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        while ($row = mysqli_fetch_assoc($hostel_sql)) {
                            $stureg_id = $row['stureg_id'];
                            $studentno = $row['studentno'];
                            $hostel = $row['hos_id'];
                            $bed = (!empty($row['bed_no'])) ? "F".$row['floor_no']."-R".$row['room_no']."-B".$row['bed_no'] : "Please assign a bed!";
                            $i++;
                            ?>
                            <tr>
								<td><?php echo htmlspecialchars($studentno ?? ''); ?></td>
								<td><?php echo htmlspecialchars($hostel ?? ''); ?></td>
								<td><?php echo htmlspecialchars($bed ?? ''); ?></td>
								<td>
									<?php if (empty($hostel) || $hostel == '0'): ?>
										<a href="editbed.php?sid=<?php echo $stureg_id; ?>"><i class="fa fa-pencil-square-o btn" style="background:green;color:white;padding:6px;"></i></a>
									<?php else: ?>
										<a href="?delete_id=<?php echo urlencode($stureg_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fa fa-minus-circle btn" style="background:red;color:white;padding:6px;"></i></a>
									<?php endif; ?>
								</td>
							</tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (isset($rows)): ?>
            <div class="alert alert-info">No records found.</div>
        <?php endif; ?>
    </form>
</div>
<!-- footer -->
<?php include 'footer.php'; ?>
</body>
</html>