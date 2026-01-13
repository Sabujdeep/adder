<?php
// Include database connection
include "../db_conn.php";

// Include the Student class (assuming it's in a separate file, e.g., Student.php)
// If it's inline, you can remove this include and paste the class here
include "../includes/classes.php";

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize response array
$response = ['success' => false, 'message' => '', 'data' => null];

// Get the action from POST or GET
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if (empty($action)) {
    $response['message'] = 'No action specified';
    echo json_encode($response);
    exit;
}

// Instantiate the Student class
$studentObj = new Student($conn);

try {
    switch ($action) {
        case 'add':
            // Add student and qualifications
            $studentData = $_POST;
            $qualifications = isset($_POST['qualifications']) ? json_decode($_POST['qualifications'], true) : [];
            
            // Handle file upload for student
            if (isset($_FILES['file_upload'])) {
                $studentData['file_upload'] = $_FILES['file_upload'];
            }
            
            // Add student
            $student_id = $studentObj->addStudent($studentData);
            
            // Add qualifications if provided
            if (!empty($qualifications)) {
                // Handle file uploads for qualifications
                foreach ($qualifications as &$qual) {
                    if (isset($_FILES['qual_file_' . $qual['index']])) {
                        $qual['file_upload'] = $_FILES['qual_file_' . $qual['index']];
                    }
                }
                $studentObj->addQualifications($student_id, $qualifications);
            }
            
            $response['success'] = true;
            $response['message'] = 'Student added successfully';
            $response['data'] = ['student_id' => $student_id];
            break;

        case 'update':
            // Update student
            $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
            $studentData = $_POST;
            
            // Handle file upload
            if (isset($_FILES['file_upload'])) {
                $studentData['file_upload'] = $_FILES['file_upload'];
            }
            
            $studentObj->updateStudent($student_id, $studentData);
            
            $response['success'] = true;
            $response['message'] = 'Student updated successfully';
            break;

        case 'delete':
            // Delete student
            $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
            $studentObj->deleteStudent($student_id);
            
            $response['success'] = true;
            $response['message'] = 'Student deleted successfully';
            break;

        case 'get':
            // Get single student with qualifications
            $student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
            $data = $studentObj->getStudentWithQualifications($student_id);
            
            $response['success'] = true;
            $response['data'] = $data;
            break;

        case 'getAll':
            // Get all students
            $students = $studentObj->getAllStudents();
            
            $response['success'] = true;
            $response['data'] = $students;
            break;

        case 'search':
            // Search students
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $students = $studentObj->searchStudents($keyword);
            
            $response['success'] = true;
            $response['data'] = $students;
            break;

        case 'count':
            // Count total students
            $count = $studentObj->countStudents();
            
            $response['success'] = true;
            $response['data'] = ['total' => $count];
            break;

        case 'deleteQualification':
            // Delete single qualification
            $qualification_id = isset($_POST['qualification_id']) ? (int)$_POST['qualification_id'] : 0;
            $studentObj->deleteQualification($qualification_id);
            
            $response['success'] = true;
            $response['message'] = 'Qualification deleted successfully';
            break;

        default:
            $response['message'] = 'Invalid action';
            break;
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Output JSON response
echo json_encode($response);
?>