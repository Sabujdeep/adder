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

        return $student_id;
    }

    /* ===== GET ALL STUDENTS ===== */
    public function getAllStudents() {
        $stmt = $this->conn->prepare("SELECT * FROM student ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $students = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $students;
    }

    /* ===== GET STUDENT BY ID ===== */
    public function getStudentById($student_id) {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
        return $student;
    }

    /* ===== SEARCH STUDENTS ===== */
    public function searchStudents($search_term) {
        $search = "%$search_term%";
        $stmt = $this->conn->prepare("
            SELECT * FROM student 
            WHERE first_name LIKE ? 
            OR last_name LIKE ? 
            OR email LIKE ? 
            OR phone LIKE ?
            ORDER BY id DESC
        ");
        $stmt->bind_param("ssss", $search, $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $students = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $students;
    }

    /* ===== UPDATE STUDENT ===== */
    public function updateStudent($student_id, $studentData) {
        // Get current file path
        $current_student = $this->getStudentById($student_id);
        $file_path = $current_student['file_upload'];

        // Handle new file upload
        if (!empty($studentData['file_upload']['tmp_name'])) {
            // Delete old file if exists
            if (!empty($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }

            $targetDir = "uploads/students/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $file_name = time().'_'.basename($studentData['file_upload']['name']);
            $targetFile = $targetDir . $file_name;
            if (move_uploaded_file($studentData['file_upload']['tmp_name'], $targetFile)) {
                $file_path = $targetFile;
            }
        }

        $stmt = $this->conn->prepare("
            UPDATE student 
            SET first_name = ?, last_name = ?, dob = ?, address = ?, phone = ?, 
                email = ?, session_year = ?, joining_year = ?, father_name = ?, 
                adhaar_number = ?, file_upload = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssssisssi",
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
            $file_path,
            $student_id
        );

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /* ===== DELETE STUDENT ===== */
    public function deleteStudent($student_id) {
        // Get student data to delete file
        $student = $this->getStudentById($student_id);
        
        // Delete student's qualifications first
        $this->deleteQualificationsByStudentId($student_id);

        // Delete student file if exists
        if (!empty($student['file_upload']) && file_exists($student['file_upload'])) {
            unlink($student['file_upload']);
        }

        // Delete student record
        $stmt = $this->conn->prepare("DELETE FROM student WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
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

    /* ===== GET QUALIFICATIONS BY STUDENT ID ===== */
    public function getQualificationsByStudentId($student_id) {
        $stmt = $this->conn->prepare("SELECT * FROM student_qualification WHERE student_id = ? ORDER BY year_finish DESC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $qualifications = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $qualifications;
    }

    /* ===== GET QUALIFICATION BY ID ===== */
    public function getQualificationById($qualification_id) {
        $stmt = $this->conn->prepare("SELECT * FROM student_qualification WHERE id = ?");
        $stmt->bind_param("i", $qualification_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $qualification = $result->fetch_assoc();
        $stmt->close();
        return $qualification;
    }

    /* ===== UPDATE QUALIFICATION ===== */
    public function updateQualification($qualification_id, $qualData) {
        // Get current file path
        $current_qual = $this->getQualificationById($qualification_id);
        $file_path = $current_qual['file_upload_path'];

        // Handle new file upload
        if (!empty($qualData['file_upload']['tmp_name'])) {
            // Delete old file if exists
            if (!empty($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }

            $targetDir = "uploads/qualifications/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $file_name = time().'_'.basename($qualData['file_upload']['name']);
            $targetFile = $targetDir . $file_name;
            if (move_uploaded_file($qualData['file_upload']['tmp_name'], $targetFile)) {
                $file_path = $targetFile;
            }
        }

        $stmt = $this->conn->prepare("
            UPDATE student_qualification
            SET qualification_name = ?, description = ?, board_university = ?, 
                year_join = ?, year_finish = ?, file_upload_path = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssiisi",
            $qualData['qualification_name'],
            $qualData['description'],
            $qualData['board_university'],
            $qualData['year_join'],
            $qualData['year_finish'],
            $file_path,
            $qualification_id
        );

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /* ===== DELETE QUALIFICATION ===== */
    public function deleteQualification($qualification_id) {
        // Get qualification data to delete file
        $qualification = $this->getQualificationById($qualification_id);

        // Delete file if exists
        if (!empty($qualification['file_upload_path']) && file_exists($qualification['file_upload_path'])) {
            unlink($qualification['file_upload_path']);
        }

        // Delete record
        $stmt = $this->conn->prepare("DELETE FROM student_qualification WHERE id = ?");
        $stmt->bind_param("i", $qualification_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /* ===== DELETE ALL QUALIFICATIONS BY STUDENT ID ===== */
    public function deleteQualificationsByStudentId($student_id) {
        // Get all qualifications to delete files
        $qualifications = $this->getQualificationsByStudentId($student_id);
        
        foreach ($qualifications as $qual) {
            if (!empty($qual['file_upload_path']) && file_exists($qual['file_upload_path'])) {
                unlink($qual['file_upload_path']);
            }
        }

        // Delete all records
        $stmt = $this->conn->prepare("DELETE FROM student_qualification WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /* ===== GET STUDENT WITH QUALIFICATIONS ===== */
    public function getStudentWithQualifications($student_id) {
        $student = $this->getStudentById($student_id);
        if ($student) {
            $student['qualifications'] = $this->getQualificationsByStudentId($student_id);
        }
        return $student;
    }
}
?>