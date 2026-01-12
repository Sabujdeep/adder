<?php
session_start();
include "../db_conn.php";  // Database connection

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'add';

$studentData = [];
$qualificationData = [];

// ---------- EDIT MODE ----------
if ($action === 'edit' && $student_id > 0) {
    // Fetch student data
    $stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $studentData = $result->fetch_assoc();

    // Fetch qualification data
    $stmt = $conn->prepare("SELECT * FROM student_qualification WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $qualificationData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ---------- FORM SUBMIT ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $studentData = [
        'first_name'    => $_POST['first_name'] ?? '',
        'last_name'     => $_POST['last_name'] ?? '',
        'dob'           => $_POST['dob'] ?? '',
        'address'       => $_POST['address'] ?? '',
        'phone'         => $_POST['phone'] ?? '',
        'email'         => $_POST['email'] ?? '',
        'session_year'  => $_POST['session_year'] ?? '',
        'joining_year'  => $_POST['joining_year'] ?? '',
        'father_name'   => $_POST['father_name'] ?? '',
        'adhaar_number' => $_POST['adhaar_number'] ?? '',
        'file_upload'   => $_FILES['student_file']['name'] ?? ''
    ];

    // Add or Update student
    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO student 
            (first_name,last_name,dob,address,phone,email,session_year,joining_year,father_name,adhaar_number,file_upload)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssssss", ...array_values($studentData));
        $stmt->execute();
        $student_id = $stmt->insert_id; // get the inserted student id
    } elseif ($action === 'edit') {
        $stmt = $conn->prepare("UPDATE student SET first_name=?, last_name=?, dob=?, address=?, phone=?, email=?, session_year=?, joining_year=?, father_name=?, adhaar_number=?, file_upload=? WHERE student_id=?");
        $stmt->bind_param("sssssssssssi", 
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
            $studentData['file_upload'],
            $student_id
        );
        $stmt->execute();

        // Delete old qualifications to reinsert
        $stmt = $conn->prepare("DELETE FROM student_qualification WHERE student_id=?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
    }

    // Insert qualifications
    if (!empty($_POST['qualification_name'])) {
        foreach ($_POST['qualification_name'] as $i => $val) {
            $stmt = $conn->prepare("INSERT INTO student_qualification 
                (student_id, qualification_name, description, board_university, year_join, year_finish, file_upload_path)
                VALUES (?,?,?,?,?,?,?)");
            $fileName = $_FILES['qualification_file']['name'][$i] ?? '';
            $stmt->bind_param("isssiis", $student_id, $_POST['qualification_name'][$i], $_POST['description'][$i], $_POST['board_university'][$i], $_POST['year_join'][$i], $_POST['year_finish'][$i], $fileName);
            $stmt->execute();
        }
    }

    $_SESSION['student_msg'] = "Student and qualifications saved successfully!";
    header("Location: manage_student.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo ucfirst($action); ?> Student</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>

        .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #4f46e5;
    padding: 12px 25px;
    color: #fff;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px; /* space between header and form */
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
* {
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
}

body {
    background: linear-gradient(120deg, #f0f4ff, #f8f9fa);
    margin: 0;
    padding: 40px;
}

h2 {
    text-align: center;
    color: #1f2937;
    margin-bottom: 25px;
}

/* MAIN CARD */
form {
    background: #ffffff;
    max-width: 1200px;
    margin: auto;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

/* FORM GRID */
.form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

label {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 6px;
    color: #374151;
}

/* INPUTS */
input[type="text"],
input[type="email"],
input[type="number"],
input[type="date"],
textarea {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: 0.2s;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
}

/* FILE INPUT */
input[type="file"] {
    border: 1px dashed #c7d2fe;
    padding: 8px;
    border-radius: 8px;
    background: #f8faff;
}

/* SECTION TITLE */
.section-title {
    margin: 30px 0 15px;
    font-size: 18px;
    color: #111827;
    border-left: 5px solid #4f46e5;
    padding-left: 10px;
}

/* QUALIFICATION ROW */
.qualification-row {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    padding: 12px;
    margin-bottom: 12px;
    border-radius: 10px;
    transition: 0.2s;
}

.qualification-row:hover {
    background: #eef2ff;
}

/* COLUMN WIDTHS */
.q-name { width: 140px; }
.q-board { width: 160px; }
.q-desc { width: 220px; height: 38px; resize: none; }
.q-year { width: 90px; }
.q-file { width: 160px; }

/* BUTTONS */
button {
    cursor: pointer;
    border-radius: 8px;
    border: none;
    font-size: 14px;
}

.add-btn {
    background: #4f46e5;
    color: #fff;
    padding: 10px 16px;
    margin-top: 10px;
}

.add-btn:hover {
    background: #4338ca;
}

.remove-btn {
    background: #ef4444;
    color: white;
    padding: 8px 10px;
}

.remove-btn:hover {
    background: #dc2626;
}

.submit-btn {
    display: block;
    margin: 30px auto 0;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    padding: 12px 40px;
    font-size: 16px;
}

.submit-btn:hover {
    opacity: 0.9;
}

    </style>
</head>

<body>
    <!-- HEADER NAVIGATION -->
<header>
    <nav class="navbar">
        <div class="logo">Student Management</div>
        <ul class="nav-links">
            <li><a href="index.php">🏠 Home</a></li>
            <li><a href="manage_student.php">📋 Manage Students</a></li>
        </ul>
    </nav>
</header>

    <div class="conatainer">
            <h2><?php echo ucfirst($action); ?> Student</h2>

<form method="post" enctype="multipart/form-data">

    <div class="form-grid">

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name"
                   value="<?= htmlspecialchars($studentData['first_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name"
                   value="<?= htmlspecialchars($studentData['last_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="dob"
                   value="<?= $studentData['dob'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone"
                   value="<?= htmlspecialchars($studentData['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($studentData['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Father Name</label>
            <input type="text" name="father_name"
                   value="<?= htmlspecialchars($studentData['father_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Session Year</label>
            <input type="number" name="session_year"
                   value="<?= htmlspecialchars($studentData['session_year'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Joining Year</label>
            <input type="number" name="joining_year"
                   value="<?= htmlspecialchars($studentData['joining_year'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Aadhaar Number</label>
            <input type="text" name="adhaar_number"
                   value="<?= htmlspecialchars($studentData['adhaar_number'] ?? '') ?>">
        </div>

        <div class="form-group" style="grid-column: span 3;">
            <label>Address</label>
            <textarea name="address"><?= htmlspecialchars($studentData['address'] ?? '') ?></textarea>
        </div>

        <div class="form-group" style="grid-column: span 3;">
            <label>Student File</label>
            <input type="file" name="student_file">

            <?php if (!empty($studentData['file_upload'])): ?>
                <small>
                    Existing file:
                    <a href="../uploads/students/<?= $studentData['file_upload'] ?>" target="_blank">
                        View
                    </a>
                </small>
            <?php endif; ?>

            <input type="hidden" name="old_student_file"
                   value="<?= htmlspecialchars($studentData['file_upload'] ?? '') ?>">
        </div>

    </div>

    <h3 class="section-title">Qualifications</h3>

    <div id="qualificationContainer">
        <?php if (!empty($qualificationData)): ?>
            <?php foreach ($qualificationData as $q): ?>
                <div class="qualification-row">

                    <input type="text" name="qualification_name[]" class="q-name"
                           value="<?= htmlspecialchars($q['qualification_name']) ?>" placeholder="Qualification">

                    <input type="text" name="board_university[]" class="q-board"
                           value="<?= htmlspecialchars($q['board_university']) ?>" placeholder="Board">

                    <textarea name="description[]" class="q-desc"
                              placeholder="Description"><?= htmlspecialchars($q['description']) ?></textarea>

                    <input type="number" name="year_join[]" class="q-year"
                           value="<?= htmlspecialchars($q['year_join']) ?>" placeholder="Join">

                    <input type="number" name="year_finish[]" class="q-year"
                           value="<?= htmlspecialchars($q['year_finish']) ?>" placeholder="Finish">

                    <input type="file" name="qualification_file[]" class="q-file">

                    <input type="hidden" name="old_qualification_file[]"
                           value="<?= htmlspecialchars($q['file_upload_path'] ?? '') ?>">

                    <button type="button" class="add-btn" onclick="addRow()">➕</button>
                    <button type="button" class="remove-btn" onclick="removeRow(this)">❌</button>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="qualification-row">
                <input type="text" name="qualification_name[]" class="q-name" placeholder="Qualification">
                <input type="text" name="board_university[]" class="q-board" placeholder="Board">
                <textarea name="description[]" class="q-desc" placeholder="Description"></textarea>
                <input type="number" name="year_join[]" class="q-year" placeholder="Join">
                <input type="number" name="year_finish[]" class="q-year" placeholder="Finish">
                <input type="file" name="qualification_file[]" class="q-file">
                <button type="button" class="add-btn" onclick="addRow()">➕</button>
                <button type="button" class="remove-btn" onclick="removeRow(this)">❌</button>
            </div>
        <?php endif; ?>
    </div>

    <button type="submit" class="submit-btn">Save Student</button>

</form>


    </div>

<script>
function addRow() {
    const container = document.getElementById('qualificationContainer');
    const div = document.createElement('div');
    div.className = 'qualification-row';

    div.innerHTML = `
        <input type="text" name="qualification_name[]" class="q-name" placeholder="Qualification">
        <input type="text" name="board_university[]" class="q-board" placeholder="Board / University">
        <textarea name="description[]" class="q-desc" placeholder="Description"></textarea>
        <input type="number" name="year_join[]" class="q-year" placeholder="Join">
        <input type="number" name="year_finish[]" class="q-year" placeholder="Finish">
        <input type="file" name="qualification_file[]" class="q-file">
        <input type="hidden" name="old_qualification_file[]" value="">
        <button type="button" class="add-btn" onclick="addRow()">➕</button>
        <button type="button" class="remove-btn" onclick="removeRow(this)">❌</button>
    `;

    container.appendChild(div);
}

function removeRow(button) {
    button.parentElement.remove();
}
</script>


</body>
</html>
