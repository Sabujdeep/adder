<?php
session_start();
include "../db_conn.php";

if (!isset($_GET['student_id'])) {
    header("Location: manage_student.php");
    exit;
}

$student_id = (int) $_GET['student_id'];

$conn->begin_transaction();

try {

    /* ================= STUDENT FILE ================= */
    $stmt = $conn->prepare("SELECT file_upload FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!empty($student['file_upload'])) {
        $path = "../uploads/students/" . $student['file_upload'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /* ================= QUALIFICATION FILES ================= */
    $stmt = $conn->prepare("SELECT file_upload_path FROM student_qualification WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if (!empty($row['file_upload_path'])) {
            $qpath = "../uploads/qualifications/" . $row['file_upload_path'];
            if (file_exists($qpath)) {
                unlink($qpath);
            }
        }
    }

    /* ================= DELETE QUALIFICATIONS ================= */
    $stmt = $conn->prepare("DELETE FROM student_qualification WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    /* ================= DELETE STUDENT ================= */
    $stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    $conn->commit();

    $_SESSION['student_msg'] = 'Student deleted successfully';

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['student_msg'] = '<div class="alert" style="background:#fee2e2;color:#991b1b;">
        Error deleting student
    </div>';
}

header("Location: manage_student.php");
exit;
