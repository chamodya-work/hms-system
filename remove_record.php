<?php
// Include database connection
include 'connection/connect.php'; // Make sure to adjust the path if necessary

// Check if the request is POST and contains the necessary data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['stureg_id'])) {
    $stureg_id = $_POST['stureg_id'];

    // Validate and sanitize input
    $stureg_id = intval($stureg_id);

    // Get the bed_id associated with the stureg_id
    $sql = "SELECT bed_id FROM registration WHERE stureg_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $stureg_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_bed_id = $row['bed_id'];

        // Update bed_id to 0 and set the bed as available
        $sql2 = "UPDATE registration SET bed_id = 0 WHERE stureg_id = ?; 
                 UPDATE hostel_bed SET availability = 1 WHERE bed_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ii", $stureg_id, $current_bed_id);
        
        if ($stmt2->execute()) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "No record found to delete";
    }

    $stmt->close();
    $stmt2->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
