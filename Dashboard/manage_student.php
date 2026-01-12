<?php
session_start();
include "../db_conn.php";  // your database connection

// Fetch all students
$sql = "SELECT * FROM student ORDER BY student_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <style>
* {
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #4f46e5;
    padding: 12px 25px;
    color: #fff;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.logo {
    font-weight: 700;
    font-size: 20px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 15px;
}

.nav-links li a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background 0.2s;
}

.nav-links li a:hover {
    background: #4338ca;
}

body {
    background: linear-gradient(120deg, #f0f4ff, #f8f9fa);
    margin: 0;
    padding: 40px;
}

h1 {
    text-align: center;
    color: #1f2937;
    margin-bottom: 25px;
}

/* PAGE CARD */
.page-card {
    background: #ffffff;
    max-width: 1400px;
    margin: auto;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

/* TOP ACTIONS */
.top-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.top-actions a {
    text-decoration: none;
    background: #4f46e5;
    color: #fff;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
}

.top-actions a:hover {
    background: #4338ca;
}

/* ALERT */
.alert {
    padding: 12px 16px;
    background: #ecfdf5;
    color: #065f46;
    border-left: 5px solid #10b981;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

thead {
    background: #eef2ff;
}

thead th {
    padding: 12px;
    text-align: left;
    color: #1e3a8a;
    font-weight: 600;
    border-bottom: 2px solid #c7d2fe;
}

tbody td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
    vertical-align: middle;
}

tbody tr:hover {
    background: #f9fafb;
}

/* ACTION BUTTONS */
.action-btn {
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 13px;
    margin-right: 5px;
}

.edit-btn {
    background: #2563eb;
    color: #fff;
}

.edit-btn:hover {
    background: #1d4ed8;
}

.delete-btn {
    background: #ef4444;
    color: #fff;
}

.delete-btn:hover {
    background: #dc2626;
}

/* FILE LINK */
.file-link {
    color: #4f46e5;
    font-weight: 600;
    text-decoration: none;
}

.file-link:hover {
    text-decoration: underline;
}

/* EMPTY */
.no-data {
    text-align: center;
    padding: 20px;
    color: #6b7280;
}
</style>

</head>
<body>
    <nav class="navbar">
        <div class="logo">Student Management</div>
        <ul class="nav-links">
            <li><a href="index.php">🏠 Home</a></li>
            <li><a href="manage_student.php">📋 Manage Students</a></li>
            <!-- You can add more links here -->
        </ul>
    </nav>

    <h1>Manage Students</h1>

   <div class="top-actions">
    <!-- <a href="index.php" class="action-btn">🏠 Home</a> -->
    <a href="add_student.php" class="action-btn">➕ Add Student</a>
</div>

    <?php
        if (!empty($_SESSION['student_msg'])) {
            echo $_SESSION['student_msg'];
            $_SESSION['student_msg'] = "";
        }
    ?>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>DOB</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Session Year</th>
                <th>Joining Year</th>
                <th>Father Name</th>
                <th>Aadhaar Number</th>
                <th>Student File</th>
                <th>Actions</th> <!-- NEW COLUMN -->
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['dob']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['session_year']); ?></td>
                        <td><?php echo htmlspecialchars($row['joining_year']); ?></td>
                        <td><?php echo htmlspecialchars($row['father_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['adhaar_number']); ?></td>
                        <td>
                            <?php if(!empty($row['file_upload'])): ?>
                                <a href="uploads/students/<?php echo $row['file_upload']; ?>" target="_blank">View File</a>
                            <?php else: ?>
                                No File
                            <?php endif; ?>
                        </td>
                        <!-- Actions column -->
                        <td>
                            <a href="add_student.php?action=edit&student_id=<?php echo $row['student_id']; ?>">Edit</a> |
                            <a href="delete_student.php?student_id=<?php echo $row['student_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13">No students found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
