<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'title' => 'Access Denied',
        'message' => 'Please login to access this page.'
    ];
    header("Location: ../login.php");
    exit();
}

// Get admin name for display
$admin_name = $_SESSION['admin_name'] ?? 'Admin';


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
    $studentFileName = $_POST['old_student_file'] ?? '';

            if (isset($_FILES['student_file']) && $_FILES['student_file']['error'] === 0) {

                $uploadDir = "../uploads/students/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($_FILES['student_file']['name'], PATHINFO_EXTENSION);
                $studentFileName = time() . "_student." . $ext;

                move_uploaded_file(
                    $_FILES['student_file']['tmp_name'],
                    $uploadDir . $studentFileName
                );
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
        'file_upload'   => $studentFileName,

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            font-family: "Inter", "Segoe UI", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            /* padding: 20px; */
            min-height: 100vh;
        }

        .navbar {
            margin-top:20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 16px 30px;
            color: #1f2937;
            /* border-radius: 12px; */
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .logo {
            font-weight: 700;
            font-size: 22px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 8px;
            margin: 0;
            padding: 0;
        }

        .nav-links li a {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-links li a:hover {
            background: #f3f4f6;
            color: #667eea;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            color: #fff;
            margin: 0 0 30px 0;
            font-size: 32px;
            font-weight: 700;
        }

        form {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .form-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            border-left: 4px solid #667eea;
            padding-left: 12px;
            margin: 0 0 20px 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-grid.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        textarea {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input[type="file"] {
            border: 2px dashed #c7d2fe;
            padding: 12px;
            border-radius: 10px;
            background: #f8faff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        input[type="file"]:hover {
            border-color: #667eea;
            background: #eff0ff;
        }

        .file-note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        .file-note a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .file-note a:hover {
            text-decoration: underline;
        }

        /* QUALIFICATIONS TABLE */
        .qualifications-wrapper {
            overflow-x: auto;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: linear-gradient(135deg, #f3f4f6, #ffffff);
            border-top: 2px solid #e5e7eb;
            border-bottom: 2px solid #e5e7eb;
        }

        table th {
            padding: 14px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            font-size: 14px;
        }

        table tbody tr:hover {
            background: #f9fafb;
        }

        .q-index {
            width: 50px;
            text-align: center;
            color: #9ca3af;
            font-weight: 600;
        }

        /* QUALIFICATION ROWS */
        .qualification-row {
            display: grid;
            grid-template-columns: auto 1fr 1fr 1fr 100px 100px 160px auto;
            gap: 12px;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            padding: 16px;
            margin-bottom: 12px;
            border-radius: 12px;
            align-items: flex-end;
            transition: all 0.3s ease;
        }

        .qualification-row:hover {
            background: #eff0ff;
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.08);
        }

        .qualification-row .form-group {
            margin: 0;
        }

        .qualification-row label {
            font-size: 11px;
        }

        .qualification-row input,
        .qualification-row textarea {
            padding: 10px 12px;
            font-size: 13px;
        }

        .q-actions {
            display: flex;
            gap: 8px;
            padding-bottom: 2px;
        }

        /* BUTTONS */
        button {
            cursor: pointer;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .add-btn {
            /* background: linear-gradient(135deg, #667eea, #764ba2); */
            color: #fff;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .remove-btn {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .remove-btn:hover {
            background: #fecaca;
            transform: translateY(-2px);
        }

        .submit-btn {
            display: block;
            margin: 40px auto 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 14px 48px;
            font-size: 16px;
            font-weight: 600;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
        }

        .empty-state {
            text-align: center;
            color: #9ca3af;
            padding: 30px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .qualification-row {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            form {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">👨‍🎓 Student Management</div>
            <ul class="nav-links">
                <li><a href="index.php">🏠 Dashboard</a></li>
                <li><a href="manage_student.php">📋 Manage Students</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2><?php echo ucfirst($action); ?> Student</h2>

        <form method="post" enctype="multipart/form-data" id='studentForm' onsubmit="return validateForm()">

            <!-- STUDENT INFORMATION SECTION -->
            <div class="form-section">
                <h3 class="section-title">Personal Information</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" required
                               value="<?= htmlspecialchars($studentData['first_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required
                               value="<?= htmlspecialchars($studentData['last_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob"
                               value="<?= $studentData['dob'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                            <input type="email" name="email" id="email" oninput="validateEmail()"
                                value="<?= htmlspecialchars($studentData['email'] ?? '') ?>">
                            <span class="error-message" id="emailError"></span>
                    </div>

                    <div class="form-group">
                         <label>Phone (10 digits)</label>
                            <input type="text" name="phone" id="phone" maxlength="10" oninput="validatePhone()"
                                value="<?= htmlspecialchars($studentData['phone'] ?? '') ?>">
                            <span class="error-message" id="phoneError"></span>
                    </div>

                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name"
                               value="<?= htmlspecialchars($studentData['father_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                       <label>Aadhaar Number (12 digits)</label>
                            <input type="text" name="adhaar_number" id="adhaar_number" maxlength="12" oninput="validateAadhaar()"
                                value="<?= htmlspecialchars($studentData['adhaar_number'] ?? '') ?>">
                            <span class="error-message" id="aadhaarError"></span>
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
                </div>

                <div class="form-grid full">
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="4"><?= htmlspecialchars($studentData['address'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-grid full">
                    <div class="form-group">
                        <label>Student Document</label>
                        <input type="file" name="student_file">
                        <?php if (!empty($studentData['file_upload'])): ?>
                            <div class="file-note">
                                📄 Current file: <a href="../uploads/students/<?= $studentData['file_upload'] ?>" target="_blank">View</a>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="old_student_file"
                               value="<?= htmlspecialchars($studentData['file_upload'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- QUALIFICATIONS SECTION -->
            <div class="form-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 class="section-title" style="margin: 0;">Qualifications</h3>
                </div>

                <div id="qualificationContainer">
                    <?php if (!empty($qualificationData)): ?>
                        <?php foreach ($qualificationData as $idx => $q): ?>
                            <div class="qualification-row">
                                <div style="align-self: center; color: #9ca3af; font-weight: 600; font-size: 13px;"><?= $idx + 1 ?></div>

                                <div class="form-group">
                                    <label>Qualification</label>
                                    <input type="text" name="qualification_name[]" placeholder="e.g., B.Tech, MBA"
                                        value="<?= htmlspecialchars($q['qualification_name']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>Board / University</label>
                                    <input type="text" name="board_university[]" placeholder="e.g., IIT Delhi"
                                        value="<?= htmlspecialchars($q['board_university']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description[]" placeholder="Brief description" rows="1"><?= htmlspecialchars($q['description']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Year Join</label>
                                    <input type="number" name="year_join[]" placeholder="2020"
                                        value="<?= htmlspecialchars($q['year_join']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>Year Finish</label>
                                    <input type="number" name="year_finish[]" placeholder="2024"
                                        value="<?= htmlspecialchars($q['year_finish']) ?>">
                                </div>

                                <input type="hidden" name="old_qualification_file[]"
                                    value="<?= htmlspecialchars($q['file_upload_path']) ?>">

                                <div class="q-actions">
                                    <button type="button" class="add-btn" onclick="addRow()">➕</button>
                                    <button type="button" class="remove-btn" onclick="removeRow(this)">✕</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="qualification-row">
                            <div style="align-self: center; color: #9ca3af; font-weight: 600; font-size: 13px;">1</div>

                            <div class="form-group">
                                <label>Qualification</label>
                                <input type="text" name="qualification_name[]" placeholder="e.g., Secondary, H.S">
                            </div>

                            <div class="form-group">
                                <label>Board / University</label>
                                <input type="text" name="board_university[]" placeholder="e.g., ICSE, CBSE">
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description[]" placeholder="Brief description" rows="1"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Year Join</label>
                                <input type="number" name="year_join[]" placeholder="2020">
                            </div>

                            <div class="form-group">
                                <label>Year Finish</label>
                                <input type="number" name="year_finish[]" placeholder="2024">
                            </div>

                            <input type="hidden" name="old_qualification_file[]" value="">

                            <div class="form-group">
                                <label>Actions</label>
                                <div class="q-actions">
                                <button type="button" class="add-btn" onclick="addRow()">➕</button>
                                <button type="button" class="remove-btn" onclick="removeRow(this)">✕</button>
                            </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="submit-btn">💾 Save Student</button>

        </form>
    </div>
    <script src="validation.js"></script>
<!-- script for adding new now -->
<script>
let qualificationCount = document.querySelectorAll('.qualification-row').length;

function addRow() {
    const container = document.getElementById('qualificationContainer');
    qualificationCount++;
    const div = document.createElement('div');
    div.className = 'qualification-row';

    div.innerHTML = `
        <div style="align-self: center; color: #9ca3af; font-weight: 600; font-size: 13px;">${qualificationCount}</div>
        <div class="form-group">
            <label>Qualification</label>
            <input type="text" name="qualification_name[]" placeholder="e.g., B.Tech, MBA">
        </div>
        <div class="form-group">
            <label>Board / University</label>
            <input type="text" name="board_university[]" placeholder="e.g., IIT Delhi">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description[]" placeholder="Brief description" rows="1"></textarea>
        </div>
        <div class="form-group">
            <label>Year Join</label>
            <input type="number" name="year_join[]" placeholder="2020">
        </div>
        <div class="form-group">
            <label>Year Finish</label>
            <input type="number" name="year_finish[]" placeholder="2024">
        </div>
        <input type="hidden" name="old_qualification_file[]" value="">
        <div class="q-actions">
            <button type="button" class="add-btn" onclick="addRow()">➕</button>
            <button type="button" class="remove-btn" onclick="removeRow(this)">✕</button>
        </div>
    `;

    container.appendChild(div);
}

function removeRow(button) {
    const container = document.getElementById('qualificationContainer');
    const rows = container.getElementsByClassName('qualification-row');

    if (rows.length > 1) {
        button.closest('.qualification-row').remove();
        updateRowNumbers();
    } else {
        alert("At least one qualification is required.");
    }
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('.qualification-row');
    rows.forEach((row, idx) => {
        const numDiv = row.querySelector('div:first-child');
        numDiv.textContent = idx + 1;
    });
    qualificationCount = rows.length;
}
</script>

</body>
</html>