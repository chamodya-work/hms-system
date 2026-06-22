<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: account/login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
  <!-- header-->
    <?php include 'header.php'; ?>
    <div class="container" >
        <h2 class="text-center page-title"><i class="fa fa-building"></i> Hostel List</h2>
        
        <!--Form starts here-->
        <form id="hoslist" action=""  method="post" class="main-form">
            <div class="form-group" >
                <table class="table table-hover hostel-table" style="width:75%;margin:auto;">
                    <thead>
                        <tr>
                            <th>Hostel</th>
                            <th>Gender</th>
                            <th>Total Beds</th>
                            <th>Beds Available</th>
                            <!--<th>Edit</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $hostel = "SELECT h.hos_id, h.gender, COUNT(hb.bed_id) AS tot_beds, SUM(hb.availability) AS free_beds FROM hostel h LEFT JOIN hostel_bed hb ON h.hos_id = hb.hos_id WHERE h.hos_id!='0' GROUP BY h.hos_id, h.gender; ";
                                        
                            $hostel_sql = mysqli_query($conn, $hostel);
                                                        
                            while ( $hostel_raw=mysqli_fetch_assoc($hostel_sql)) {
                                
                                $hos_id = $hostel_raw['hos_id'];
                                $gender = $hostel_raw['gender'];
                                $tot_beds = $hostel_raw['tot_beds'];
                                $free_beds = $hostel_raw['free_beds'];
                                
                        ?>
                    
                        <tr>
                            <td><?php echo htmlspecialchars($hos_id); ?></td>
                            <td><?php echo htmlspecialchars($gender); ?></td>
                            <td><?php echo htmlspecialchars($tot_beds); ?></td>
                            <td><?php echo htmlspecialchars($free_beds); ?></td>
                            <!--<td><a href="edithostel.php?hos_id=<?php echo $hos_id ?>"><i class="fa fa-pencil-square-o btn edit-icon"></i></a></td>-->
                        </tr>
                        <?php       
                            }
                        ?>      
                    </tbody>
                </table>
            </div>
            <!--
            <div class="form-group" style="text-align:center; margin-top: 30px;">
                <a href="addhostel.php" class="btn-add"><i class="fa fa-plus-circle"></i> Add New Hostel</a>
            </div>
            -->
        </form> 
    <!--footer-->   
    </div>
    
    <!-- footer -->
    <?php include 'footer.php'; ?>
    
 </body>
</html>