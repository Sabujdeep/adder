<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../db_conn.php";

header('Content-Type: application/json');

$data = [];

$sql = "SELECT * FROM student ORDER BY student_id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = [
            'student_id'    => $row['student_id'],
            'first_name'    => $row['first_name'],
            'last_name'     => $row['last_name'],
            'dob'           => $row['dob'],
            'address'       => $row['address'],
            'phone'         => $row['phone'],
            'email'         => $row['email'],
            'session_year'  => $row['session_year'],
            'joining_year'  => $row['joining_year'],
            'father_name'   => $row['father_name'],
            'adhaar_number' => $row['adhaar_number'],
            'file_upload'   => $row['file_upload']
        ];
    }
}

echo json_encode($data);
?>
