<?php
session_start();
include("connection/connect.php");
date_default_timezone_set("Asia/Colombo");

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: account/login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // --- Step 1: Get old academic year string ---
    $stmt = $conn->prepare("SELECT academic_year FROM academic_year WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        echo "<script>alert('Academic year not found.'); window.location='addacayr.php';</script>";
        exit;
    }
    $row = $result->fetch_assoc();
    $old_academic_year = $row['academic_year'];
    $stmt->close();

    // --- Step 2: Calculate next academic year (e.g., 2024/2025 -> 2025/2026) ---
    $years = explode('/', $old_academic_year);
    $next_academic_year = ($years[0] + 1) . '/' . ($years[1] + 1);

    // --- Step 3: Check if the next academic year exists ---
    $stmt = $conn->prepare("SELECT id FROM academic_year WHERE academic_year = ?");
    $stmt->bind_param("s", $next_academic_year);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        echo "<script>alert('Next academic year ($next_academic_year) does not exist! Please add it first.'); window.location='addacayr.php';</script>";
        exit;
    }
    $stmt->close();

    // --- Step 4: Mark the old year as promoted ---
    $stmt = $conn->prepare("UPDATE academic_year SET is_promoted = 1, promoted_on = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // --- Step 5: Get all bed IDs occupied by students in the old year ---
    $stmt = $conn->prepare("SELECT bed_id FROM registration WHERE applying_acayr = ? AND bed_id IS NOT NULL AND bed_id != 0");
    $stmt->bind_param("s", $old_academic_year);
    $stmt->execute();
    $bed_result = $stmt->get_result();
    $bed_ids = [];
    while ($bed_row = $bed_result->fetch_assoc()) {
        $bed_ids[] = (int)$bed_row['bed_id'];
    }
    $stmt->close();

    // --- Step 6: Free those beds (set availability = 1) ---
    if (!empty($bed_ids)) {
        $placeholders = implode(',', array_fill(0, count($bed_ids), '?'));
        $types = str_repeat('i', count($bed_ids));
        $sql = "UPDATE hostel_bed SET availability = 1 WHERE bed_id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$bed_ids);
        $stmt->execute();
        $stmt->close();
    }

    // --- Step 7: Insert new student records for the new academic year (preserve history) ---

    // First, check if any students already exist for the new year (to avoid duplicates)
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM registration WHERE applying_acayr = ?");
    $stmt->bind_param("s", $next_academic_year);
    $stmt->execute();
    $cnt_result = $stmt->get_result();
    $cnt_row = $cnt_result->fetch_assoc();
    $stmt->close();

    if ($cnt_row['cnt'] == 0) {
        // No records yet for the new year – safe to insert copies
        // Prepare the INSERT ... SELECT query
        $insert_sql = "
            INSERT INTO registration (
                applying_acayr, regdate, batch, course, studentno, email, gender,
                distance, scholarships, shol_account, lowincome, m_job, m_jobcat,
                m_salary, m_otherincome, m_totincome, m_paysheet, m_paysheet_tmp,
                f_job, f_jobcat, f_salary, f_otherincome, f_totincome, f_paysheet,
                f_paysheet_tmp, g_job, g_jobcat, g_salary, g_otherincome, g_totincome,
                g_paysheet, g_paysheet_tmp, income_certificate, income_certificate_tmp,
                medical, med_cat, med_desc, med_file, med_file_tmp, siblings, eligibility,
                admit, bed_id, payslip, payslip_tmp, payment, e_contact_name,
                e_contact_number, e_contact_relation
            )
            SELECT 
                ? AS applying_acayr,
                NOW() AS regdate,
                batch, course, studentno, email, gender,
                distance, scholarships, shol_account, lowincome, m_job, m_jobcat,
                m_salary, m_otherincome, m_totincome, m_paysheet, m_paysheet_tmp,
                f_job, f_jobcat, f_salary, f_otherincome, f_totincome, f_paysheet,
                f_paysheet_tmp, g_job, g_jobcat, g_salary, g_otherincome, g_totincome,
                g_paysheet, g_paysheet_tmp, income_certificate, income_certificate_tmp,
                medical, med_cat, med_desc, med_file, med_file_tmp, siblings, eligibility,
                0 AS admit,
                NULL AS bed_id,
                payslip, payslip_tmp, payment, e_contact_name, e_contact_number, e_contact_relation
            FROM registration 
            WHERE applying_acayr = ?
        ";

        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ss", $next_academic_year, $old_academic_year);
        if ($stmt->execute()) {
            $inserted = $stmt->affected_rows;
            $stmt->close();
            $message = "Academic year promoted successfully! $inserted student(s) copied to $next_academic_year. Beds have been freed.";
        } else {
            $error = $stmt->error;
            $stmt->close();
            $message = "Error copying students: $error";
        }
    } else {
        // Records already exist for the new year – do not duplicate
        $message = "Promotion completed. Students already exist for $next_academic_year. Beds freed (if any).";
    }

    // --- Step 8: Redirect with message ---
    echo "<script>alert('$message'); window.location='addacayr.php';</script>";
    exit;

} else {
    header("location: addacayr.php");
    exit;
}
?>