<?php
class Student
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* ============================
       HANDLE STUDENT FILE UPLOAD
    ============================ */
    private function uploadStudentFile(array $file, string $oldFile = ''): string
    {
        if (!isset($file) || $file['error'] !== 0) {
            return $oldFile;
        }

        $uploadDir = "../uploads/students/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = time() . "_student." . $ext;

        move_uploaded_file($file['tmp_name'], $uploadDir . $fileName);

        return $fileName;
    }

    /* ============================
       ADD / UPDATE STUDENT
    ============================ */
    public function saveStudent(array $post, array $files, string $action, int $student_id = 0): int
    {
        $studentFile = $this->uploadStudentFile(
            $files['student_file'] ?? [],
            $post['old_student_file'] ?? ''
        );

        $data = [
            $post['first_name'] ?? '',
            $post['last_name'] ?? '',
            $post['dob'] ?? '',
            $post['address'] ?? '',
            $post['phone'] ?? '',
            $post['email'] ?? '',
            $post['session_year'] ?? '',
            $post['joining_year'] ?? '',
            $post['father_name'] ?? '',
            $post['adhaar_number'] ?? '',
            $studentFile
        ];

        if ($action === 'add') {

            $stmt = $this->conn->prepare(
                "INSERT INTO student 
                (first_name,last_name,dob,address,phone,email,session_year,joining_year,father_name,adhaar_number,file_upload)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)"
            );

            $stmt->bind_param("sssssssssss", ...$data);
            $stmt->execute();

            $student_id = $stmt->insert_id;

        } else {

            $stmt = $this->conn->prepare(
                "UPDATE student SET
                first_name=?, last_name=?, dob=?, address=?, phone=?, email=?,
                session_year=?, joining_year=?, father_name=?, adhaar_number=?, file_upload=?
                WHERE student_id=?"
            );

            $stmt->bind_param("sssssssssssi", ...$data, $student_id);
            $stmt->execute();

            $stmt = $this->conn->prepare(
                "DELETE FROM student_qualification WHERE student_id=?"
            );
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
        }

        $this->saveQualifications($student_id, $post, $files);

        return $student_id;
    }

    /* ============================
       SAVE QUALIFICATIONS
    ============================ */
    private function saveQualifications(int $student_id, array $post, array $files): void
    {
        if (empty($post['qualification_name'])) {
            return;
        }

        foreach ($post['qualification_name'] as $i => $val) {

            $fileName = $files['qualification_file']['name'][$i] ?? '';

            $stmt = $this->conn->prepare(
                "INSERT INTO student_qualification
                (student_id, qualification_name, description, board_university, year_join, year_finish, file_upload_path)
                VALUES (?,?,?,?,?,?,?)"
            );

            $stmt->bind_param(
                "isssiis",
                $student_id,
                $post['qualification_name'][$i],
                $post['description'][$i],
                $post['board_university'][$i],
                $post['year_join'][$i],
                $post['year_finish'][$i],
                $fileName
            );

            $stmt->execute();
        }
    }

    /* ============================
       FETCH DATA (EDIT MODE)
    ============================ */
    public function getStudent(int $student_id): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE student_id=?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ?? [];
    }

    public function getQualifications(int $student_id): array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM student_qualification WHERE student_id=?"
        );
        $stmt->bind_param("i", $student_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}


<!-- ------------------------------------------------------------------------------------- -->


2️⃣ Use the class in your existing page

At the top of your add_student file (after DB connection):

require_once "../classes/Student.php";

$studentObj = new Student($conn);




3️⃣ Replace your POST logic with THIS

✅ This is the only logic you need now

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id = $studentObj->saveStudent(
        $_POST,
        $_FILES,
        $action,
        $student_id
    );

    $_SESSION['student_msg'] = "Student and qualifications saved successfully!";
    header("Location: manage_student.php");
    exit;
}


4️⃣ Replace edit-mode fetch logic

Instead of raw SQL:

if ($action === 'edit' && $student_id > 0) {
    $studentData = $studentObj->getStudent($student_id);
    $qualificationData = $studentObj->getQualifications($student_id);
}