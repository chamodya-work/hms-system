<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}
if ($_SESSION["cat"] != '4') {
    header("location: index.php");
    exit;
}

$supervisor_category = $_SESSION["supervisor_category"];

?>

<!doctype html>
<html lang="en">

<body>
    <!-- header-->
    <?php include 'header.php'; ?>
    <h2 class="text-center page-title"><i class="fa fa-users"></i>Assigned Work</h2>

    <div style="width: 70%;margin:auto;">


        <!--Form starts here-->
        <div id="usermanagement" action="" method="post" class="needs-validation" novalidate>

            <!-- Input Row -->
            <div class="row justify-content-right mb-4">

                <div class="col-md-3 col-lg-3 col align-self-end">
                    <!-- Form starts here -->
                    <form method="post">

                        <div class="row justify-content-right mb-4">
                            <div class="">

                                <select class="form-control" name="status_filter" onchange="this.form.submit()">
                                    <option value="">Filter by</option>
                                    <option value="Pending" <?= (isset($_POST['status_filter']) && $_POST['status_filter'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="Informed" <?= (isset($_POST['status_filter']) && $_POST['status_filter'] == 'Informed') ? 'selected' : '' ?>>Informed</option>
                                    <option value="In Progress" <?= (isset($_POST['status_filter']) && $_POST['status_filter'] == 'In Progress') ? 'selected' : '' ?>>In Progress
                                    </option>
                                    <option value="Completed" <?= (isset($_POST['status_filter']) && $_POST['status_filter'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="Closed" <?= (isset($_POST['status_filter']) && $_POST['status_filter'] == 'Closed') ? 'selected' : '' ?>>Closed</option>
                                </select>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Requested Date</th>
                                <th>Hostel</th>
                                <th>Floor No</th>
                                <th>Room No</th>
                                <th>Bed No</th>
                                <th>Nature</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            function option($value, $current, $disable = false)
                            {
                                $selected = ($current === $value) ? 'selected' : '';

                                // Only disable if NOT current
                            
                                return "<option value=\"$value\" $selected >"
                                    . ucfirst(str_replace('_', ' ', $value))
                                    . "</option>";
                            }

                            include("connection/connect.php");


                            $status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';

                            $query = "SELECT rep_id,req_date, r.bed_id, hb.hos_id, hb.floor_no, hb.room_no, nature,supervisor_review, description, status 
          FROM repairs r 
          INNER JOIN hostel_bed hb ON r.bed_id = hb.bed_id WHERE r.cat = '$supervisor_category'";

                            if (!empty($status_filter) && $status_filter != 'none') {
                                $status_filter = $conn->real_escape_string($status_filter);
                                $query .= " AND r.status = '$status_filter'";
                            }

                            $query .= " ORDER BY req_date DESC";

                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $current = $row['status'];
                                    $isUpdateReview = $row['supervisor_review'] == "" ? "Submit" : "Update";
                                    $disable = $row['status'] =="Completed" ? "" : "disabled";

                                    echo "<tr>";
                                    echo "<td>" . date('d M Y', strtotime($row['req_date'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["hos_id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['floor_no']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["room_no"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['bed_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nature"]) . "</td>";
                                    echo "<td style='width:25%;'>" . htmlspecialchars($row["description"]) . "</td>";
                                    echo "<td>";
                                    echo "<form method='post' action='assign_form.php'>";
                                    echo "<input type='hidden' name='rep_id' value='" . $row['rep_id'] . "'>";
                                    echo "<select name='status' class='form-control' " . ($current == 'Closed' ? 'disabled' : '') . " onchange='this.form.submit()'>";

                                    echo "<option value='Pending' " . ($row['status'] == 'Pending' ? 'selected' : '') . " disabled>Pending</option>";
                                    echo "<option value='Informed' " . ($row['status'] == 'Informed' ? 'selected' : '') . " disabled>Informed</option>";
                                    echo "<option value='In Progress' " . ($row['status'] == 'In Progress' ? 'selected' : '') . " >In Progress</option>";
                                    echo "<option value='Completed' " . ($row['status'] == 'Completed' ? 'selected' : '') . " >Completed</option>";
                                    echo "<option value='Closed' " . ($row['status'] == 'Closed' ? 'selected' : '') . " disabled>Closed</option>";

                                    echo "</select>";
                                    echo "<td>
                                            <form method='post' action='assign_form.php'>
                                                <input type='hidden' name='rep_id' value='" . $row['rep_id'] . "'  />
                                                <textarea name='supervisor_review' $disable  placeholder='Leave a review...' required >" . htmlspecialchars($row['supervisor_review']) . "</textarea>
                                                <br>
                                                <button type='submit' name='supervisor_review_request' $disable  class='btn btn-success btn-sm'> $isUpdateReview the Review</button>
                                            </form>
                                        </td>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'> No repairs found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- footer-->
            <?php include 'footer.php'; ?>
</body>

</html>