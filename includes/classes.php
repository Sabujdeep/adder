<?php
include "../db_conn.php"; // your DB connection

class Student {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /* ===== ADD STUDENT ===== */
    public function addStudent($studentData) {
        // Handle file upload
        $file_path = '';
        if (!empty($studentData['file_upload']['tmp_name'])) {
            $targetDir = "uploads/students/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $file_name = time().'_'.basename($studentData['file_upload']['name']);
            $targetFile = $targetDir . $file_name;
            if (move_uploaded_file($studentData['file_upload']['tmp_name'], $targetFile)) {
                $file_path = $targetFile;
            }
        }

        $stmt = $this->conn->prepare("
            INSERT INTO student 
            (first_name, last_name, dob, address, phone, email, session_year, joining_year, father_name, adhaar_number, file_upload) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssisss",
            $studentData['first_name'],
            $studentData['last_name'],
            $studentData['dob'],
            $studentData['address'],
            $studentData['phone'],
            $studentData['email'],
            $studentData['session_year'],
            $studentData['joining_year'],
            $studentData['father_name'],
            $studentData['adhaar_number'],
            $file_path
        );

        $stmt->execute();
        $student_id = $stmt->insert_id;
        $stmt->close();

        return $student_id; // Return student ID to insert qualifications
    }

    /* ===== ADD MULTIPLE QUALIFICATIONS ===== */
    public function addQualifications($student_id, $qualifications) {
        foreach ($qualifications as $qual) {
            // Handle file upload
            $file_path = '';
            if (!empty($qual['file_upload']['tmp_name'])) {
                $targetDir = "uploads/qualifications/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                $file_name = time().'_'.basename($qual['file_upload']['name']);
                $targetFile = $targetDir . $file_name;
                if (move_uploaded_file($qual['file_upload']['tmp_name'], $targetFile)) {
                    $file_path = $targetFile;
                }
            }

            $stmt = $this->conn->prepare("
                INSERT INTO student_qualification
                (student_id, qualification_name, description, board_university, year_join, year_finish, file_upload_path)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "isssiis",
                $student_id,
                $qual['qualification_name'],
                $qual['description'],
                $qual['board_university'],
                $qual['year_join'],
                $qual['year_finish'],
                $file_path
            );

            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
