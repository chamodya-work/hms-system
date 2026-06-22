<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}
// Language switch
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}


?>
<!doctype html>
<html lang="en">
<!-- header-->
<?php
include 'header.php';
include 'categoryMapping.php';

$repair_columns = [];
$repair_columns_result = mysqli_query($conn, "SHOW COLUMNS FROM repairs");
if ($repair_columns_result) {
    while ($column = mysqli_fetch_assoc($repair_columns_result)) {
        $repair_columns[] = $column['Field'];
    }
}

$has_review = in_array('review', $repair_columns);
$has_supervisor_review = in_array('supervisor_review', $repair_columns);
$has_file_path = in_array('file_path', $repair_columns);

?>
<div style="width: 70%;margin:auto;">
    <div style="margin-bottom: 30px;">
        <h2 class="text-center"><br>Maintenance & Repair</h2><br><br>
    </div>


    <?php
    //show maintenance
    $review_select = $has_review ? "review" : "'' AS review";
    $supervisor_review_select = $has_supervisor_review ? "supervisor_review" : "'' AS supervisor_review";
    $file_path_select = $has_file_path ? "file_path" : "'' AS file_path";

    $repair = "SELECT rep_id, req_date, cat,room_no,hos_id,floor_no,description, status,$review_select,$supervisor_review_select,$file_path_select FROM repairs LEFT JOIN hostel_bed ON repairs.bed_id = hostel_bed.bed_id  ";

    if ($_SESSION['cat'] == 1) {
        $student_no = isset($_SESSION['student_data']['data']['StudentNumber']) ? $_SESSION['student_data']['data']['StudentNumber'] : '';
        $repair .= " WHERE stu_no = '" . mysqli_real_escape_string($conn, $student_no) . "'";
    }

    $repair .= " ORDER BY rep_id DESC";



    $repair_sql = mysqli_query($conn, $repair);
    if (mysqli_num_rows($repair_sql) > 0) {
        ?>
        <form method="post" action="pdfprint.php">
            <div style="margin-top:2rem;">
                <div>
                    <?php

                    $html = '<table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Request Date</th>
                                            <th>Hostel</th>
                                            <th>Room No</th>
                                            <th>Category</th>
                                            <th>Nature of the request</th>
                                            <th>Status</th>
                                            <th>Supervisor Review</th>
                                            <th>File Evidence</th>
                                            <th>Review</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                    while ($repair_raw = mysqli_fetch_assoc($repair_sql)) {
                        $rid = $repair_raw['rep_id'];
                        $req_date = $repair_raw['req_date'];
                        $cat = $repair_raw['cat'];
                        $description = $repair_raw['description'];
                        $status = $repair_raw['status'];
                        $lang = $_SESSION['lang'];
                        $hostel = $repair_raw['hos_id'];
                        $room = $repair_raw['room_no'];
                        $supervisor_review = $repair_raw['supervisor_review'];
                        $review = $repair_raw['review'];
                        $filePath = $repair_raw['file_path'];


                        // Handle language
                        $categoryText = ($lang == 'en') ? $cat : $complain[$cat];
                        $statusText = ($lang == 'en') ? $status : $status_[$status];

                        // Edit button
                        $editBtn = '';
                        if ($_SESSION['cat'] == '2' && $status != "Completed") {
                            $editBtn = "<a href='repair.php?rid=$rid'>
                                <i class='fa fa-pencil-square-o btn' style='background:green;color:white;padding:6px;'></i>
                            </a>";
                        }

                        $html .= "
                        <tr>
                            <td>$req_date</td>
                            <td>$hostel</td>
                            <td>$room</td>
                            <td>$categoryText</td>
                            <td>$description</td>
                            <td>$statusText</td>
                            <td>$supervisor_review</td>
                            <td>";
                                
                            $html .= $filePath=="" ? "No file uploaded" : "<a href='$filePath' target='_blank'>View File</a>";
                            $html .= "</td>";

                    $disable = ($statusText == "Completed" && $has_review) ? "" : "disabled";
                    $isUpdateReview = $review==""?"Submit":"Update";
                    if ($_SESSION['cat'] == 1) {
                        $html .= "<td>
                        <form method='post' action='pdfprint.php'>
                            <input type='hidden' name='rep_id' value='$rid'  />
                            <textarea name='review'  placeholder='Leave a review...' required $disable>$review</textarea>
                            <br>
                            <button type='submit' name='review_request' $disable class='btn btn-success btn-sm'> $isUpdateReview the Review</button>
                        </form>
                       </td>";
                    }else{
                        $html .= "<td>$editBtn</td>";
                    }
                    }
                    $html .= "</tr></tbody></table>";
                    echo $html;

                    ?>
                </div>
            </div>
            <textarea name="html" hidden><?php echo htmlspecialchars($html); ?></textarea>
            <?php
            if ($_SESSION['cat'] != 1) {
                echo "<button type='submit' name='export_pdf'>Export to PDF</button>";
            }
            ?>

        </form>

        <?php
    } else {
        echo "<p class='text-center'>No maintenance or repair requests found.</p>";
    }


    ?>


</div>
<!-- footer -->
<?php include 'footer.php'; ?>



</body>

</html>
